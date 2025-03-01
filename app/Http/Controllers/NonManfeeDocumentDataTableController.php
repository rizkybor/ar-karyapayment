<?php

namespace App\Http\Controllers;

use App\Models\NonManfeeDocument;
use Illuminate\Support\Facades\Auth;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class NonManfeeDocumentDataTableController extends Controller
{
    /**
     * Mengambil data untuk DataTables
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = NonManfeeDocument::query()
            ->with(['contract', 'accumulatedCosts'])
            ->where('created_by', $userId)
            ->select('non_manfee_documents.*');

        return DataTables::eloquent($query)
            ->addIndexColumn() // ✅ Tambahkan ini agar DT_RowIndex dikenali

            ->addColumn('termin_invoice', function ($row) {
                return $row->contract ? $row->contract->termin_invoice : '-';
            })
            ->addColumn('total', function ($row) {
                // Ambil akumulasi biaya pertama jika ada
                $firstAccumulatedCost = $row->accumulatedCosts->first();

                // Pastikan ada data, jika tidak tampilkan Rp 0,00
                return $firstAccumulatedCost
                    ? 'Rp ' . number_format($firstAccumulatedCost->total, 2, ',', '.')
                    : 'Rp 0,00';
            })

            // FILTER SEARCH untuk `contract.contract_number`
            ->filterColumn('contract.contract_number', function ($query, $keyword) {
                $query->whereHas('contract', function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(contract_number) LIKE ?', ["%" . strtolower($keyword) . "%"]);
                });
            })

            // 🔍 FILTER SEARCH hanya untuk `contract.employee_name`
            ->filterColumn('contract.employee_name', function ($query, $keyword) {
                $query->whereHas('contract', function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(employee_name) LIKE ?', ["%" . strtolower($keyword) . "%"]);
                });
            })

            // 🛑 Hapus filterColumn untuk `total` karena bukan field di database
            ->rawColumns(['action'])
            ->make(true);
    }
}
