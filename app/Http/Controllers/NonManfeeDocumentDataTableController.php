<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use App\Models\NonManfeeDocument;

use Yajra\DataTables\Facades\DataTables;


class NonManfeeDocumentDataTableController extends Controller
{
    /**
     * Mengambil data untuk DataTables dengan caching Redis
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 🛑 Hapus Cache saat ada Filtering / Searching
        if ($request->has('search') && !empty($request->search['value'])) {
            Cache::forget('nonmanfee_doc_datatable_' . $user->id);
        }

        $cacheKey = 'nonmanfee_doc_datatable' . $user->id;
        if (
            Cache::has($cacheKey) && !$request->has('search') && !$request->has('columns')
            && !$request->has('created_by') && !$request->has('date_start') && !$request->has('date_end')
        ) {
            return Cache::get($cacheKey);
        }

        // ✅ Query utama
        // $query = NonManfeeDocument::query()
        //     ->with(['contract', 'accumulatedCosts'])
        //     ->where(function ($query) use ($user) {
        //         $query->where('created_by', $user->id)
        //             ->orWhereHas('approvals', function ($q) use ($user) {
        //                 $q->where('approver_id', $user->id);
        //             });
        //     })
        //     ->select('non_manfee_documents.*')
        //     ->orderByRaw("
        //     CASE 
        //         WHEN expired_at >= NOW() THEN 0 
        //         ELSE 1
        //     END, 
        //     expired_at ASC
        // ");

        // ✅ Query utama diganti (hanya 'perbendaharaan', 'manager_anggaran', 'direktur_keuangan' yang dapat melihat seluruh dokumen yang terbuat)
        $query = NonManfeeDocument::query()
            ->with(['contract', 'accumulatedCosts'])
            ->select('non_manfee_documents.*');

        // Cek apakah user memiliki role teknisi
        /** @var \App\Models\User $user */
        if ($user->hasRole(['perbendaharaan', 'manager_anggaran', 'direktur_keuangan', 'pajak'])) {

            // User teknisi bisa melihat semua dokumen, tidak ada filter tambahan
        } else {
            // Jika bukan teknisi, tetap pakai filter by created_by atau approvals
            $query->where(function ($query) use ($user) {
                $query->where('created_by', $user->id)
                    ->orWhereHas('approvals', function ($q) use ($user) {
                        $q->where('approver_id', $user->id);
                    });
            });
        }

        $query->orderByRaw("
            CASE 
                WHEN expired_at >= NOW() THEN 0 
                ELSE 1
            END, 
            expired_at ASC
        ");

        // Filter created_by jika ada
        if ($request->has('created_by') && $request->created_by != '') {
            $query->where('created_by', $request->created_by);
        }

        // Filter date range jika ada
        if ($request->has('date_start') || $request->has('date_end')) {
            $startDate = $request->date_start ?: '1970-01-01';
            $endDate = $request->date_end ?: date('Y-m-d');

            // Tambahkan 1 hari ke endDate untuk mencakup seluruh hari terakhir
            $endDate = date('Y-m-d', strtotime($endDate . ' +1 day'));

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Dropdown Dibuat Oleh
        if ($request->ajax() && $request->has('get_users')) {
            // Ambil SEMUA user dengan role 'maker' (tanpa memperhatikan apakah punya dokumen)
            $users = \App\Models\User::where('role', 'maker')
                ->orderBy('name')
                ->get(['id', 'name']);

            return response()->json($users);
        }

        // Handle filter dari request DataTables
        if ($request->has('columns')) {
            foreach ($request->columns as $column) {
                if ($column['searchable'] === "true" && !empty($column['search']['value'])) {
                    $columnIndex = $column['data'];
                    $searchValue = $column['search']['value'];

                    switch ($columnIndex) {
                        case '4': // Status
                            $this->filterStatus($query, $searchValue);
                            break;
                        case '5': // Nama Pemberi Kerja
                            $query->whereHas('contract', function ($q) use ($searchValue) {
                                $q->where('employee_name', 'like', '%' . $searchValue . '%');
                            });
                            break;
                        case '3': // Nomor Kontrak
                            $query->whereHas('contract', function ($q) use ($searchValue) {
                                $q->where('contract_number', 'like', '%' . $searchValue . '%');
                            });
                            break;
                    }
                }
            }
        }

        // jika ingin di seragamkan by status datanya 
        // CASE 
        //   WHEN status = '0' THEN 1
        //   WHEN status = '1' THEN 2
        //   WHEN status = '2' THEN 3
        //   WHEN status = '3' THEN 4
        //   WHEN status = '4' THEN 5
        //   WHEN status = '5' THEN 6
        //   WHEN status = '6' THEN 7
        //   ELSE 8
        // END, 

        // ✅ Gunakan DataTables untuk proses data
        $data = DataTables::eloquent($query)
            ->addIndexColumn()

            ->addColumn('invoice_number', function ($row) {
                return $row->invoice_number ? $row->invoice_number : '-';
            })

            ->addColumn('termin_invoice', function ($row) {
                return $row->contract ? $row->contract->termin_invoice : '-';
            })

            ->addColumn('status', function ($row) {
                return view('components.label-status-table', ['status' => $row->status])->render();
            })

            ->addColumn('total', function ($row) {
                $firstAccumulatedCost = $row->accumulatedCosts->first();
                return $firstAccumulatedCost
                    ? 'Rp ' . number_format($firstAccumulatedCost->total, 2, ',', '.')
                    : 'Rp 0,00';
            })

            // 🔍 FILTERING
            ->filterColumn('invoice_number', function ($query, $keyword) {
                $query->whereRaw('LOWER(invoice_number) LIKE ?', ["%" . strtolower($keyword) . "%"]);
            })

            ->filterColumn('status', function ($query, $keyword) {
                $this->filterStatus($query, $keyword);
            })

            ->filterColumn('expired_at', function ($query, $keyword) {
                $keyword = strtolower($keyword);

                if ($keyword === 'expired') {
                    $query->where('expired_at', '<', now());
                    return;
                }

                // Coba cocokkan format tanggal (misal: 01-06-2025 atau 2025-06-01)
                if (strtotime($keyword)) {
                    $query->whereDate('expired_at', '=', date('Y-m-d', strtotime($keyword)));
                    return;
                }

                // Pencarian berdasarkan angka tanggal
                if (preg_match('/^\d{1,2}$/', $keyword)) {
                    $query->whereDay('expired_at', $keyword);
                    return;
                }

                // Pencarian berdasarkan angka bulan (01–12)
                if (preg_match('/^(0?[1-9]|1[0-2])$/', $keyword)) {
                    $query->whereMonth('expired_at', $keyword);
                    return;
                }

                // Pencarian berdasarkan tahun
                if (preg_match('/^\d{4}$/', $keyword)) {
                    $query->whereYear('expired_at', $keyword);
                    return;
                }

                // Pencarian berdasarkan nama bulan (bahasa Indonesia)
                $monthNames = [
                    'januari' => 1,
                    'februari' => 2,
                    'maret' => 3,
                    'april' => 4,
                    'mei' => 5,
                    'juni' => 6,
                    'juli' => 7,
                    'agustus' => 8,
                    'september' => 9,
                    'oktober' => 10,
                    'november' => 11,
                    'desember' => 12,
                ];

                foreach ($monthNames as $name => $num) {
                    if (str_contains($keyword, $name)) {
                        $query->whereMonth('expired_at', $num);
                        break;
                    }
                }
            })

            ->filterColumn('contract.contract_number', function ($query, $keyword) {
                $query->whereHas('contract', function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(contract_number) LIKE ?', ["%" . strtolower($keyword) . "%"]);
                });
            })

            ->filterColumn('contract.title', function ($query, $keyword) {
                $query->whereHas('contract', function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(title) LIKE ?', ["%" . strtolower($keyword) . "%"]);
                });
            })

            ->filterColumn('contract.employee_name', function ($query, $keyword) {
                $query->whereHas('contract', function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(employee_name) LIKE ?', ["%" . strtolower($keyword) . "%"]);
                });
            })

            ->rawColumns(['status'])
            ->make(true);

        // 🚀 Simpan hasil query ke Redis selama 1 jam (hanya jika tidak ada pencarian)
        if (
            !$request->has('search') && !$request->has('columns')
            && !$request->has('created_by') && !$request->has('date_start') && !$request->has('date_end')
        ) {
            Cache::put($cacheKey, $data, 3600);
        }

        return $data;
    }

    // Helper method untuk filter status
    private function filterStatus($query, $keyword)
    {
        $statusMapping = [
            '0' => '0',
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6',
            'draft' => '0',
            'checked by kadiv' => '1',
            'checked by perbendaharaan' => '2',
            'checked by mgr. anggaran' => '3',
            'checked by dir. keuangan' => '4',
            'checked by pajak' => '5',
            'done' => '6',
        ];

        $keywordLower = strtolower($keyword);

        if (isset($statusMapping[$keywordLower])) {
            $query->where('status', $statusMapping[$keywordLower]);
        } else {
            foreach ($statusMapping as $text => $value) {
                if (strpos($text, $keywordLower) !== false) {
                    $query->where('status', $value);
                    return;
                }
            }
        }
    }

    /**
     * Hapus cache saat data berubah (Insert, Update, Delete)
     */
    public function clearCache()
    {
        $user = Auth::user();
        Cache::forget('nonmanfee_doc_datatable_' . $user->id);
    }
}
