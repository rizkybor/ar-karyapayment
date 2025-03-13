<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataFeed;
use App\Models\NonManfeeDocument;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {

        // Ambil user yang sedang login
        $user = auth()->user();

        $dataInvoicesNonFee = NonManfeeDocument::with('contract', 'accumulatedCosts')
            ->where(function ($query) use ($user) {
                $query->where('created_by', $user->id) // Dokumen dibuat oleh user login
                    ->orWhereHas('approvals', function ($q) use ($user) {
                        $q->where('approver_id', $user->id); // Dokumen yang user bisa approve
                    });
            })
            ->where('expired_at', '>=', now())
            ->select('id', 'invoice_number', 'period', 'contract_id', 'is_active', 'status', 'created_by')
            ->orderBy('expired_at', 'asc')
            ->get()
            ->map(function ($invoice) {
                return (object) [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'period' => $invoice->period,
                    'contract_number' => $invoice->contract ? $invoice->contract->contract_number : '-',
                    'employer_name' => $invoice->contract ? $invoice->contract->employee_name : '-',
                    'is_active' => $invoice->is_active,
                    'status' => $invoice->status,
                    'total' => $invoice->accumulatedCosts->sum('total') ?? 0,
                ];
            });



        // Ambil data invoices untuk Stick Chart untuk Non Management Fee dalam 6 bulan terakhir
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        // Ambil data dalam 6 bulan terakhir berdasarkan created_at & hitung total biaya dari relasi
        $dokumenSementara = NonManfeeDocument::join('non_manfee_doc_accumulated_costs', 'non_manfee_documents.id', '=', 'non_manfee_doc_accumulated_costs.document_id')
            ->selectRaw('
            DATE_FORMAT(non_manfee_documents.created_at, "%Y-%m") as month_year,
            DATE_FORMAT(non_manfee_documents.created_at, "%b") as month, 
            DATE_FORMAT(non_manfee_documents.created_at, "%Y") as year, 
            SUM(non_manfee_doc_accumulated_costs.total) as total
        ')
            ->where('non_manfee_documents.created_at', '>=', $sixMonthsAgo)
            ->where('non_manfee_documents.created_by', $user->id) // Filter berdasarkan user yang login

            ->groupBy('month_year', 'month', 'year')
            ->orderBy('month_year', 'asc')
            ->get()
            ->map(function ($doc) {
                return (object) [
                    'month' => $doc->month,
                    'year' => $doc->year,
                    'total' => $doc->total ?? 0,
                ];
            });

        // Ambil data untuk Pie Chart
        $activeCount = $dataInvoicesNonFee->where('is_active', 1)->count();
        $notActiveCount = $dataInvoicesNonFee->where('is_active', 0)->count();
        $rejectedCount = $dataInvoicesNonFee->where('status', 103)->count();
        $completedCount = $dataInvoicesNonFee->where('status', 100)->count();

        $totalInvoices = $activeCount + $notActiveCount + $rejectedCount + $completedCount;

        return view('pages/dashboard/dashboard', compact('dokumenSementara', 'dataInvoicesNonFee', 'activeCount', 'notActiveCount', 'rejectedCount', 'completedCount', 'totalInvoices'));
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
