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
        Log::info('FUNGSI UPDATE:', $request->all());
        DB::beginTransaction();
        $documents = $request->input('documents', []);

        if (empty($documents)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada invoice yang dipilih'
            ], 400);
        }

        try {
            $manfeeUpdated = 0;
            $nonManfeeUpdated = 0;
            $statusValues = [];

            $records = collect($documents)->map(function ($doc) {
                if ($doc['type'] === 'Management Fee') {
                    $doc['model'] = ManfeeDocument::find($doc['id']);
                } elseif ($doc['type'] === 'Non Management Fee') {
                    $doc['model'] = NonManfeeDocument::find($doc['id']);
                }
                return $doc;
            })->filter(fn($d) => $d['model']);

            $statusValues = $records->pluck('model')->pluck('status_print')->all();
            $count = count($statusValues);

            $allTrue = collect($statusValues)->every(fn($s) => $s == true);
            $allFalse = collect($statusValues)->every(fn($s) => $s == false);

            foreach ($records as $doc) {
                $model = $doc['model'];

                if ($count === 1) {
                    $model->status_print = !$model->status_print;
                } else {
                    if (!$allTrue && !$allFalse) {
                        $model->status_print = false;
                    } elseif ($allTrue) {
                        $model->status_print = false;
                    } elseif ($allFalse) {
                        $model->status_print = true;
                    }
                }

                $model->save();

                if ($doc['type'] === 'Management Fee') {
                    $manfeeUpdated++;
                } elseif ($doc['type'] === 'Non Management Fee') {
                    $nonManfeeUpdated++;
                }
            }

            DB::commit();

            $totalUpdated = $manfeeUpdated + $nonManfeeUpdated;

            return response()->json([
                'success' => true,
                'message' => $totalUpdated > 0
                    ? "Berhasil update status print $totalUpdated invoice"
                    : "Tidak ada invoice yang diubah",
                'updated' => $totalUpdated,
                'manfee_updated' => $manfeeUpdated,
                'non_manfee_updated' => $nonManfeeUpdated
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

    public function getBulkInvoiceData(Request $request)
    {
        $invoiceNumbers = collect($request->input('invoice_numbers', []))
            ->filter()
            ->map(function ($num) {
                return trim($num);
            })
            ->unique()
            ->values();

        if ($invoiceNumbers->isEmpty()) {
            return response()->json([
                'message' => 'Invoice number tidak valid.',
            ], 400);
        }

        $manfee = ManfeeDocument::whereIn('invoice_number', $invoiceNumbers)
            ->select('id', 'invoice_number')
            ->get()
            ->map(function ($doc) {
                $doc->type = 'Management Fee';
                return $doc;
            });

        Log::info('MF rows matched:', $manfee->pluck('invoice_number')->toArray());


        $nonManfee = NonManfeeDocument::whereIn('invoice_number', $invoiceNumbers)
            ->select('id', 'invoice_number')
            ->get()
            ->map(function ($doc) {
                $doc->type = 'Non Management Fee';
                return $doc;
            });

        Log::info('NF rows matched:', $nonManfee->pluck('invoice_number')->toArray());

        $merged = $manfee->concat($nonManfee);

        Log::info('Merged documents:', $merged->toArray());

        return response()->json([
            'data' => $merged->values()
        ]);
    }


    public function datatable(Request $request)
    {
        $manfeeQuery = ManfeeDocument::where('status', 6)->select([
            'id',
            DB::raw("'Management Fee' as type"),
            'invoice_number',
            DB::raw('CAST(status_print AS SIGNED) as status_print'),
            'created_at'
        ]);

        $nonManfeeQuery = NonManfeeDocument::where('status', 6)->select([
            'id',
            DB::raw("'Non Management Fee' as type"),
            'invoice_number',
            DB::raw('CAST(status_print AS SIGNED) as status_print'),
            'created_at'
        ]);

        // $query = DB::table($manfeeQuery->unionAll($nonManfeeQuery));

        $unionQuery = $manfeeQuery->unionAll($nonManfeeQuery);
        $query = DB::query()
            ->fromSub($unionQuery, 'documents')
            ->orderBy('status_print', 'asc')
            ->orderBy('created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('status_print', function ($row) {
                // Cek nilai secara ketat
                $status = $row->status_print === true || $row->status_print === 1 || $row->status_print === '1';
                return $status
                    ? '<span class="text-green-600 font-semibold">Sudah diprint</span>'
                    : '<span class="text-red-600 font-semibold">Belum diprint</span>';
            })
            ->rawColumns(['status_print'])
            ->make(true);
    }
}
