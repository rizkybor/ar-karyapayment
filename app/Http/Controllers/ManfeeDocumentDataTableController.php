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

    // 🛑 Hapus Cache saat ada Filtering / Searching
    if ($request->has('search') && !empty($request->search['value'])) {
      Cache::forget('manfee_doc_datatable_' . $user->id);
    }

    $cacheKey = 'manfee_doc_datatable_' . $user->id;
    if (Cache::has($cacheKey) && !$request->has('search')) {
      return Cache::get($cacheKey);
    }

    // ✅ Query utama
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
    Cache::forget('manfee_doc_datatable_' . $user->id);
  }
}
