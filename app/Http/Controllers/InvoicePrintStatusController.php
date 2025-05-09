<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
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
        DB::beginTransaction();
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada invoice yang dipilih'
            ], 400);
        }

        try {

            $manfeeUpdated = ManfeeDocument::whereIn('id', $ids)
                ->where('status_print', false)
                ->update(['status_print' => true]);

            $nonManfeeUpdated = NonManfeeDocument::whereIn('id', $ids)
                ->where('status_print', false)
                ->update(['status_print' => true]);

            DB::commit();

            $totalUpdated = $manfeeUpdated + $nonManfeeUpdated;

            return response()->json([
                'success' => true,
                'message' => $totalUpdated > 0
                    ? "Berhasil update status print invoice"
                    : "Tidak ada invoice yang perlu diupdate",
                'updated' => $totalUpdated
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal update status print: ' . $e->getMessage()
            ], 500);
        }
    }

    public function datatable(Request $request)
    {
        $manfeeQuery = ManfeeDocument::select([
            'id',
            DB::raw("'Management Fee' as type"),
            'invoice_number',
            DB::raw('CAST(status_print AS SIGNED) as status_print'),
            'created_at'
        ]);

        $nonManfeeQuery = NonManfeeDocument::select([
            'id',
            DB::raw("'Non Management Fee' as type"),
            'invoice_number',
            DB::raw('CAST(status_print AS SIGNED) as status_print'),
            'created_at'
        ]);

        $query = DB::table($manfeeQuery->unionAll($nonManfeeQuery));

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('status_print', function ($row) {
                // Cek nilai secara ketat
                $status = $row->status_print === true || $row->status_print === 1 || $row->status_print === '1';
                return $status
                    ? '<span class="text-green-600 font-semibold">Sudah</span>'
                    : '<span class="text-red-600 font-semibold">Belum</span>';
            })
            ->rawColumns(['status_print'])
            ->make(true);
    }
}
