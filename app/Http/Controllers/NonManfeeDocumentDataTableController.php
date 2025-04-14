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

        $cacheKey = 'nonmanfee_doc_datatable_' . $user->id;
        if (Cache::has($cacheKey) && !$request->has('search')) {
            return Cache::get($cacheKey);
        }

        // ✅ Query utama
        $query = NonManfeeDocument::query()
            ->with(['contract', 'accumulatedCosts'])
            ->where(function ($query) use ($user) {
                $query->where('created_by', $user->id)
                    ->orWhereHas('approvals', function ($q) use ($user) {
                        $q->where('approver_id', $user->id);
                    });
            })
            ->select('non_manfee_documents.*')
            ->orderByRaw("
            CASE 
                WHEN expired_at >= NOW() THEN 0 
                ELSE 1
            END, 
            expired_at ASC
        ");

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
                $statusMapping = [
                    'draft' => '0',
                    'checked by kadiv' => '1',
                    'checked by pembendaharaan' => '2',
                    'checked by mgr. anggaran' => '3',
                    'checked by dir. keuangan' => '4',
                    'checked by pajak' => '5',
                    'done' => '6',
                ];

                $keywordLower = strtolower($keyword);

                // Jika keyword cocok dengan status yang dimapping
                if (isset($statusMapping[$keywordLower])) {
                    $query->where('status', $statusMapping[$keywordLower]);
                } else {
                    // Jika user mencari kata "manager", tetap cocokkan ke "manager anggaran"
                    foreach ($statusMapping as $text => $value) {
                        if (strpos($text, $keywordLower) !== false) {
                            $query->where('status', $value);
                            return;
                        }
                    }
                }
            })

            ->filterColumn('expired_at', function ($query, $keyword) {
                if (strtolower($keyword) === 'expired') {
                    $query->where('expired_at', '<', now());
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
        if (!$request->has('search')) {
            Cache::put($cacheKey, $data, 3600);
        }

        return $data;
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
