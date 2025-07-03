<?php

namespace App\Http\Controllers;

use App\Models\ManfeeDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class ManfeeDocumentDataTableController extends Controller
{
  public function index(Request $request)
  {
    $user = Auth::user();

    // Hapus Cache saat ada Filtering / Searching
    if ($request->has('search') && !empty($request->search['value'])) {
      Cache::forget('manfee_doc_datatable_' . $user->id);
    }

    $cacheKey = 'manfee_doc_datatable_' . $user->id;
    if (
      Cache::has($cacheKey) && !$request->has('search') && !$request->has('columns')
      && !$request->has('created_by') && !$request->has('date_start') && !$request->has('date_end')
    ) {
      return Cache::get($cacheKey);
    }

    // Query utama dengan filter
    $query = ManfeeDocument::query()
      ->with(['contract', 'accumulatedCosts'])
      ->where(function ($query) use ($user) {
        $query->where('created_by', $user->id)
          ->orWhereHas('approvals', function ($q) use ($user) {
            $q->where('approver_id', $user->id);
          });
      })
      ->select('manfee_documents.*')
      ->orderByRaw("
                CASE 
                    WHEN expired_at >= NOW() THEN 0 
                    ELSE 1 
                END, expired_at ASC
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

    if ($request->ajax() && $request->has('get_users')) {
      // Ambil semua user yang pernah membuat dokumen
      $userIds = ManfeeDocument::groupBy('created_by')->pluck('created_by');

      // Ambil data user lengkap
      $users = \App\Models\User::whereIn('id', $userIds)->get();

      return response()->json($users->map(function ($user) {
        return [
          'id' => $user->id,
          'name' => $user->name
        ];
      }));
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

    $data = DataTables::eloquent($query)
      ->addIndexColumn()

      ->addColumn('invoice_number', function ($row) {
        return $row->invoice_number ?: '-';
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
      ->filterColumn('contract.contract_number', function ($query, $keyword) {
        $query->whereHas('contract', function ($q) use ($keyword) {
          $q->where('contract_number', 'like', '%' . $keyword . '%');
        });
      })
      ->filterColumn('contract.title', function ($query, $keyword) {
        $query->whereHas('contract', function ($q) use ($keyword) {
          $q->where('title', 'like', '%' . $keyword . '%');
        });
      })
      ->filterColumn('contract.employee_name', function ($query, $keyword) {
        $query->whereHas('contract', function ($q) use ($keyword) {
          $q->where('employee_name', 'like', '%' . $keyword . '%');
        });
      })
      ->rawColumns(['status'])
      ->make(true);

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

  public function clearCache()
  {
    $user = Auth::user();
    Cache::forget('manfee_doc_datatable_' . $user->id);
  }
}
