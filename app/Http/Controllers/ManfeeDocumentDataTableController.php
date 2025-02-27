<?php

namespace App\Http\Controllers;

use App\Models\ManfeeDocument;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class ManfeeDocumentDataTableController extends Controller
{
  /**
   * Mengambil data untuk DataTables
   */
  public function index(Request $request)
  {
    $query = ManfeeDocument::query()
      ->with('contract')
      ->select('manfee_documents.*');

    return DataTables::eloquent($query)
      ->addIndexColumn() // ✅ Tambahkan ini agar DT_RowIndex dikenali
      ->addColumn('contract.contract_number', function ($row) {
        return $row->contract ? $row->contract->contract_number : '-';
      })
      ->addColumn('contract.employee_name', function ($row) {
        return $row->contract ? $row->contract->employee_name : '-';
      })
      ->addColumn('contract.value', function ($row) {
        return $row->contract && is_numeric($row->contract->value)
          ? (float) $row->contract->value
          : 0.00;
      })
      ->addColumn('termin_invoice', function ($row) {
        return $row->contract ? $row->contract->termin_invoice : '-';
      })
      ->addColumn('total', function ($row) {
        return '-'; // Tidak bisa difilter, hanya sebagai tampilan
      })

      // FILTER SEARCH untuk `contract.contract_number`
      ->filterColumn('contract.contract_number', function ($query, $keyword) {
        $query->whereHas('contract', function ($q) use ($keyword) {
          $q->whereRaw('LOWER(contract_number) LIKE ?', ["%" . strtolower($keyword) . "%"]);
        });
      })

      // FILTER SEARCH untuk `contract.employee_name`
      ->filterColumn('contract.employee_name', function ($query, $keyword) {
        $query->whereHas('contract', function ($q) use ($keyword) {
          $q->whereRaw('LOWER(employee_name) LIKE ?', ["%" . strtolower($keyword) . "%"]);
        });
      })

      // FILTER SEARCH untuk `contract.value`
      ->filterColumn('contract.value', function ($query, $keyword) {
        $query->whereHas('contract', function ($q) use ($keyword) {
          $q->whereRaw('value LIKE ?', ["%" . $keyword . "%"]);
        });
      })

      ->rawColumns(['action'])
      ->make(true);
  }
}
