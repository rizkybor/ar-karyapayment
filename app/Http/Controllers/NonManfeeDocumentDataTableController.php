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
        $user = Auth::user(); // Ambil user yang sedang login

        // Ambil dokumen yang dibuat oleh user atau yang membutuhkan approvalnya
        $query = NonManfeeDocument::with('contract')
            ->where('created_by', $user->id)
            ->orWhereHas('approvals', function ($q) use ($user) {
                $q->where('approver_id', $user->id);
            })
            ->select('non_manfee_documents.*');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('contract_number', function ($row) {
                return $row->contract ? $row->contract->contract_number : '-';
            })
            ->addColumn('employee_name', function ($row) {
                return $row->contract ? $row->contract->employee_name : '-';
            })
            ->addColumn('termin_invoice', function ($row) {
                return $row->contract ? $row->contract->termin_invoice : '-';
            })
            ->addColumn('total', function ($row) {
                return 'Rp ' . number_format($row->total ?? 0, 2, ',', '.');
            })
            ->filterColumn('contract_number', function ($query, $keyword) {
                $query->whereHas('contract', function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(contract_number) LIKE ?', ["%" . strtolower($keyword) . "%"]);
                });
            })
            ->filterColumn('employee_name', function ($query, $keyword) {
                $query->whereHas('contract', function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(employee_name) LIKE ?', ["%" . strtolower($keyword) . "%"]);
                });
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }
}