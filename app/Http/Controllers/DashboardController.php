<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataFeed;

class DashboardController extends Controller
{
    public function index()
    {
        // Dummy data untuk testing (mirip dengan hasil dari database)
        $dataInvoices = collect([
            (object) ['id' => 1, 'invoice_number' => 'INV-001', 'period' => '2024-01', 'contract_number' => 'KPU-001/2024', 'employer_name' => 'PT. Contoh Sejahtera', 'status' => 1, 'total' => 15000000],
            (object) ['id' => 2, 'invoice_number' => 'INV-002', 'period' => '2024-02', 'contract_number' => 'KPU-002/2024', 'employer_name' => 'CV. Sukses Makmur', 'status' => 0, 'total' => 12000000],
            (object) ['id' => 3, 'invoice_number' => 'INV-003', 'period' => '2024-03', 'contract_number' => 'KPU-003/2024', 'employer_name' => 'PT. Mitra Bangun', 'status' => 0, 'total' => 18500000],
            (object) ['id' => 4, 'invoice_number' => 'INV-004', 'period' => '2024-04', 'contract_number' => 'KPU-004/2024', 'employer_name' => 'UD. Jaya Sentosa', 'status' => 0, 'total' => 9500000],
            (object) ['id' => 5, 'invoice_number' => 'INV-005', 'period' => '2024-05', 'contract_number' => 'KPU-005/2024', 'employer_name' => 'PT. Mega Karya', 'status' => 100, 'total' => 21000000],
            (object) ['id' => 6, 'invoice_number' => 'INV-006', 'period' => '2024-06', 'contract_number' => 'KPU-004/2024', 'employer_name' => 'UD. Jaya Sentosa', 'status' => 99, 'total' => 9500000],
            (object) ['id' => 7, 'invoice_number' => 'INV-007', 'period' => '2024-07', 'contract_number' => 'KPU-005/2024', 'employer_name' => 'PT. Mega Karya', 'status' => 99, 'total' => 21000000],
        ]);

        // Hitung jumlah invoice berdasarkan kategori status
        $draftCount = $dataInvoices->where('status', 0)->count();
        $onProgressCount = $dataInvoices->whereIn('status', [1, 2, 3, 4, 5, 6, 9])->count();
        $completedCount = $dataInvoices->where('status', 100)->count();
        $rejectedCount = $dataInvoices->where('status', 99)->count();

        $totalInvoices = $draftCount + $onProgressCount + $completedCount + $rejectedCount;


        return view('pages/dashboard/dashboard', compact('dataInvoices', 'draftCount', 'onProgressCount', 'completedCount', 'rejectedCount', 'totalInvoices'));
    }

    /**
     * Displays the analytics screen
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function analytics()
    {
        return view('pages/dashboard/analytics');
    }

    /**
     * Displays the fintech screen
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function fintech()
    {
        return view('pages/dashboard/fintech');
    }
}
