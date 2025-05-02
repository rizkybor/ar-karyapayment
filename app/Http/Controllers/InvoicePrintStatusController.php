<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\ManfeeDocument;
use App\Models\NonManfeeDocument;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class InvoicePrintStatusController extends Controller
{
    public function index()
    {
        return view('pages.invoice-print-status.index');
    }

    public function updatePrintStatus(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'Tidak ada ID yang dipilih'], 400);
        }

        // Update untuk dua model jika digabung dari Manfee & Non-Manfee
        ManfeeDocument::whereIn('id', $ids)->update(['status_print' => 1]);
        NonManfeeDocument::whereIn('id', $ids)->update(['status_print' => 1]);

        return response()->json(['message' => 'Status print diperbarui.']);
    }

    public function datatable(Request $request)
    {
        $user = Auth::user();

        if ($request->has('search') && !empty($request->search['value'])) {
            Cache::forget('nonmanfee_doc_datatable_' . $user->id);
        }

        $cacheKey = 'nonmanfee_doc_datatable_' . $user->id;
        if (Cache::has($cacheKey) && !$request->has('search')) {
            return Cache::get($cacheKey);
        }

        $manfeeQuery = ManfeeDocument::query()
            ->selectRaw("'Manfee' as type, invoice_number, status_print, created_at");

        $nonManfeeQuery = NonManfeeDocument::query()
            ->selectRaw("'Non Manfee' as type, invoice_number, status_print, created_at");

        $mergedQuery = $manfeeQuery->unionAll($nonManfeeQuery);

        $query = DB::table(DB::raw("({$mergedQuery->toSql()}) as sub"))
            ->mergeBindings($mergedQuery->getQuery());

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('status_print', function ($row) {
                return $row->status_print == 1 ? 'Sudah' : 'Belum';
            })
            ->make(true);
    }
}
