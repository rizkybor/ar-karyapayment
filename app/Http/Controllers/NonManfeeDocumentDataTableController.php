<?php

namespace App\Http\Controllers;

use App\Models\NonManfeeDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

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