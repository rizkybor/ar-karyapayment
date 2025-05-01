<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ManfeeDocument;
use App\Models\NonManfeeDocument;
use Yajra\DataTables\Facades\DataTables;

class InvoicePrintStatusController extends Controller
{
    public function index()
    {
        return view('pages.invoice-print-status.index');
    }

    public function datatable(Request $request)
    {
        $user = Auth::user();
    
        // Query untuk manfee
        $manfeeQuery = ManfeeDocument::query()
            ->selectRaw("'Manfee' as type, invoice_number, status_print, created_at")
            ->where(function ($query) use ($user) {
                $query->where('created_by', $user->id)
                    ->orWhereHas('approvals', function ($q) use ($user) {
                        $q->where('approver_id', $user->id);
                    });
            });
    
        // Query untuk non-manfee
        $nonManfeeQuery = NonManfeeDocument::query()
            ->selectRaw("'Non Manfee' as type, invoice_number, status_print, created_at")
            ->where(function ($query) use ($user) {
                $query->where('created_by', $user->id)
                    ->orWhereHas('approvals', function ($q) use ($user) {
                        $q->where('approver_id', $user->id);
                    });
            });
    
        // Union kedua query
        $mergedQuery = $manfeeQuery->unionAll($nonManfeeQuery);
    
        // Bungkus dengan DB::table(...) agar bisa dipakai di DataTables
        $query = \DB::table(\DB::raw("({$mergedQuery->toSql()}) as sub"))
            ->mergeBindings($mergedQuery->getQuery());
    
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('status_print', function ($row) {
                return $row->status_print == 1 ? 'Sudah' : 'Belum';
            })
            ->make(true);
    }
}
