<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\ManfeeDocument;
use App\Models\NonManfeeDocument;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ✅ Ambil user yang sedang login
        $user = Auth::user();

        // ✅ Hitung jumlah dokumen expired (NonManfeeDocument + ManfeeDocument)
        $expiredCountNonManfee = NonManfeeDocument::where(function ($query) use ($user) {
            $query->where('created_by', $user->id)
                ->orWhereHas('approvals', function ($q) use ($user) {
                    $q->where('approver_id', $user->id);
                });
        })
            ->where('expired_at', '<', now())
            ->count();

        $expiredCountManfee = ManfeeDocument::where(function ($query) use ($user) {
            $query->where('created_by', $user->id)
                ->orWhereHas('approvals', function ($q) use ($user) {
                    $q->where('approver_id', $user->id);
                });
        })
            ->where('expired_at', '<', now())
            ->count();

        $totalExpiredCount = $expiredCountNonManfee + $expiredCountManfee;

        // ✅ Ambil data invoices NonManfeeDocument
        $dataInvoicesNonFee = NonManfeeDocument::with('contract', 'accumulatedCosts')
            ->where(function ($query) use ($user) {
                $query->where('created_by', $user->id)
                    ->orWhereHas('approvals', function ($q) use ($user) {
                        $q->where('approver_id', $user->id);
                    });
            })
            ->where('expired_at', '>=', now())
            ->select('id', 'invoice_number', 'period', 'contract_id', 'is_active', 'status', 'expired_at', 'created_by')
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
                    'expired_at' => $invoice->expired_at,
                    'total' => $invoice->accumulatedCosts->sum('total') ?? 0,
                ];
            });

        // ✅ Ambil data invoices ManfeeDocument
        $dataInvoicesManFee = ManfeeDocument::with('contract', 'accumulatedCosts')
            ->where(function ($query) use ($user) {
                $query->where('created_by', $user->id)
                    ->orWhereHas('approvals', function ($q) use ($user) {
                        $q->where('approver_id', $user->id);
                    });
            })
            ->where('expired_at', '>=', now())
            ->select('id', 'invoice_number', 'period', 'contract_id', 'is_active', 'status', 'expired_at', 'created_by')
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
                    'expired_at' => $invoice->expired_at,
                    'total' => $invoice->accumulatedCosts->sum('total') ?? 0,
                ];
            });

        // ✅ Gabungkan data NonManfee dan Manfee ke dalam `$dataPieChartAllInvoices`
        $dataPieChartAllInvoices = $dataInvoicesNonFee->merge($dataInvoicesManFee);

        // ✅ Ambil data invoices untuk Stick Chart dalam 6 bulan terakhir
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();

        $dataStickChartNonManfee = NonManfeeDocument::join('non_manfee_doc_accumulated_costs', 'non_manfee_documents.id', '=', 'non_manfee_doc_accumulated_costs.document_id')
            ->selectRaw('
                DATE_FORMAT(non_manfee_documents.created_at, "%Y-%m") as month_year,
                DATE_FORMAT(non_manfee_documents.created_at, "%b") as month, 
                DATE_FORMAT(non_manfee_documents.created_at, "%Y") as year, 
                SUM(non_manfee_doc_accumulated_costs.total) as total
            ')
            ->where('non_manfee_documents.created_at', '>=', $sixMonthsAgo)
            ->where('non_manfee_documents.created_by', $user->id)
            ->groupBy('month_year', 'month', 'year')
            ->orderBy('month_year', 'asc')
            ->get();

        $dataStickChartManfee = ManfeeDocument::join('manfee_doc_accumulated_costs', 'manfee_documents.id', '=', 'manfee_doc_accumulated_costs.document_id')
            ->selectRaw('
                DATE_FORMAT(manfee_documents.created_at, "%Y-%m") as month_year,
                DATE_FORMAT(manfee_documents.created_at, "%b") as month, 
                DATE_FORMAT(manfee_documents.created_at, "%Y") as year, 
                SUM(manfee_doc_accumulated_costs.total) as total
            ')
            ->where('manfee_documents.created_at', '>=', $sixMonthsAgo)
            ->where('manfee_documents.created_by', $user->id)
            ->groupBy('month_year', 'month', 'year')
            ->orderBy('month_year', 'asc')
            ->get();

        // ✅ Gabungkan data untuk Stick Chart
        $dataStickChartAllInvoices = $dataStickChartNonManfee->merge($dataStickChartManfee);

        // ✅ Ambil data untuk Pie Chart
        $activeCount = $dataPieChartAllInvoices->where('is_active', true)->count();
        $notActiveCount = $totalExpiredCount;
        $rejectedCount = $dataPieChartAllInvoices->where('status', 103)->count();
        $completedCount = $dataPieChartAllInvoices->where('status', 100)->count();

        $totalInvoices = $activeCount + $notActiveCount + $rejectedCount + $completedCount;

        // ✅ Pastikan variabel tersedia di view
        return view('pages/dashboard/dashboard', compact(
            'dataStickChartAllInvoices',
            'dataInvoicesNonFee',
            'dataInvoicesManFee',
            'dataPieChartAllInvoices',
            'notActiveCount',
            'totalInvoices'
        ));
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
