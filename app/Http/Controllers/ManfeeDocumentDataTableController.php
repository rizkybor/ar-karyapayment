<?php

namespace App\Http\Controllers;

use App\Models\ManfeeDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;


class ManfeeDocumentDataTableController extends Controller
{
  public function index(Request $request)
  {
    $user = Auth::user();

    // Query utama dengan `where(function ($query) {...})`
    $query = ManfeeDocument::query()
      ->with(['contract', 'accumulatedCosts'])
      ->where(function ($query) use ($user) {
        $query->where('created_by', $user->id) // Dokumen yang dibuat oleh user
          ->orWhereHas('approvals', function ($q) use ($user) {
            $q->where('approver_id', $user->id); // Dokumen yang user harus approve
          });
      })
      ->select('manfee_documents.*')
      ->orderByRaw("
        CASE 
            WHEN expired_at >= NOW() THEN 0 
            ELSE 1 
        END, expired_at ASC
    ");

    return DataTables::eloquent($query)
      ->addIndexColumn() // ✅ Tambahkan ini agar DT_RowIndex dikenali

      ->addColumn('invoice_number', function ($row) {
        return $row->invoice_number ? $row->invoice_number : '-';
      })

      ->addColumn('termin_invoice', function ($row) {
        return $row->contract ? $row->contract->termin_invoice : '-';
      })

      // ✅ Tambahkan kolom status dengan komponen Blade
      ->addColumn('status', function ($row) {
        return view('components.label-status-table', ['status' => $row->status])->render();
      })

      ->addColumn('total', function ($row) {
        // Ambil akumulasi biaya pertama jika ada
        $firstAccumulatedCost = $row->accumulatedCosts->first();

        // Pastikan ada data, jika tidak tampilkan Rp 0,00
        return $firstAccumulatedCost
          ? 'Rp ' . number_format($firstAccumulatedCost->total, 2, ',', '.')
          : 'Rp 0,00';
      })

      // 🔍 FILTER SEARCH untuk `invoice_number`
      ->filterColumn('invoice_number', function ($query, $keyword) {
        $query->whereRaw('LOWER(invoice_number) LIKE ?', ["%" . strtolower($keyword) . "%"]);
      })

      // FILTER SEARCH untuk `contract.contract_number`
      ->filterColumn('contract.contract_number', function ($query, $keyword) {
        $query->whereHas('contract', function ($q) use ($keyword) {
          $q->whereRaw('LOWER(contract_number) LIKE ?', ["%" . strtolower($keyword) . "%"]);
        });
      })

      // FILTER SEARCH untuk `contract.title`
      ->filterColumn('contract.title', function ($query, $keyword) {
        $query->whereHas('contract', function ($q) use ($keyword) {
          $q->whereRaw('LOWER(title) LIKE ?', ["%" . strtolower($keyword) . "%"]);
        });
      })

      // 🔍 FILTER SEARCH hanya untuk `contract.employee_name`
      ->filterColumn('contract.employee_name', function ($query, $keyword) {
        $query->whereHas('contract', function ($q) use ($keyword) {
          $q->whereRaw('LOWER(employee_name) LIKE ?', ["%" . strtolower($keyword) . "%"]);
        });
      })

      // 🛑 Hapus filterColumn untuk `total` karena bukan field di database
      ->rawColumns(['status', 'action'])
      ->make(true);
  }
}
