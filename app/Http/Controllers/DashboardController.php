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
            ->select('id', 'invoice_number', 'period', 'contract_id', 'status', 'created_by')
            ->orderBy('expired_at', 'asc')
            ->get()
            ->map(function ($invoice) {
                return (object) [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'period' => $invoice->period,
                    'contract_number' => $invoice->contract ? $invoice->contract->contract_number : '-',
                    'employer_name' => $invoice->contract ? $invoice->contract->employee_name : '-',
                    'status' => $invoice->status,
                    'total' => $invoice->accumulatedCosts->sum('total') ?? 0,
                ];
            });

        // Ambil data invoices untuk Non Management Fee dalam 6 bulan terakhir
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
            ->groupBy('month_year', 'month', 'year')
            ->orderBy('month_year', 'asc')
            ->get()
            ->map(function ($doc) {
                return (object) [
                    'created_at' => $doc->created_at,
                    'month' => $doc->month,  
                    'year' => $doc->year,  
                    'total' => $doc->total ?? 0, 
                ];
            });

        // DUMMY MENYESUAIKAN DATA SEBENARNYA 
        $dataInvoicesReal = collect([
            (object) [
                'id' => 1,
                'contract_id' => 101,
                'invoice_number' => 'INV-001',
                'receipt_number' => 'REC-001',
                'letter_number' => 'LTR-001',
                'period' => '2024-01',
                'letter_subject' => 'Tagihan Januari',
                'category' => 'management_non_fee',
                'status' => 1,
                'last_reviewers' => 'Manager',
                'is_active' => true,
                'created_by' => 1,
                'expired_at' => Carbon::parse('2024-02-01 00:01:00'),
                'contract_number' => 'KPU-001/2024',
                'employer_name' => 'PT. Contoh Sejahtera',
                'total' => 15000000
            ],
            (object) [
                'id' => 2,
                'contract_id' => 102,
                'invoice_number' => 'INV-002',
                'receipt_number' => 'REC-002',
                'letter_number' => 'LTR-002',
                'period' => '2024-02',
                'letter_subject' => 'Tagihan Februari',
                'category' => 'management_non_fee',
                'status' => 0,
                'last_reviewers' => 'Supervisor',
                'is_active' => true,
                'created_by' => 2,
                'expired_at' => Carbon::parse('2024-03-01 00:01:00'),
                'contract_number' => 'KPU-002/2024',
                'employer_name' => 'CV. Sukses Makmur',
                'total' => 12000000
            ],
            (object) [
                'id' => 3,
                'contract_id' => 103,
                'invoice_number' => 'INV-003',
                'receipt_number' => 'REC-003',
                'letter_number' => 'LTR-003',
                'period' => '2024-03',
                'letter_subject' => 'Tagihan Maret',
                'category' => 'management_non_fee',
                'status' => 0,
                'last_reviewers' => null,
                'is_active' => true,
                'created_by' => 3,
                'expired_at' => Carbon::parse('2024-04-01 00:01:00'),
                'contract_number' => 'KPU-003/2024',
                'employer_name' => 'PT. Mitra Bangun',
                'total' => 18500000
            ],
            (object) [
                'id' => 4,
                'contract_id' => 104,
                'invoice_number' => 'INV-004',
                'receipt_number' => 'REC-004',
                'letter_number' => 'LTR-004',
                'period' => '2024-04',
                'letter_subject' => 'Tagihan April',
                'category' => 'management_non_fee',
                'status' => 0,
                'last_reviewers' => null,
                'is_active' => false,
                'created_by' => 4,
                'expired_at' => Carbon::parse('2024-05-01 00:01:00'),
                'contract_number' => 'KPU-004/2024',
                'employer_name' => 'UD. Jaya Sentosa',
                'total' => 9500000
            ],
            (object) [
                'id' => 5,
                'contract_id' => 105,
                'invoice_number' => 'INV-005',
                'receipt_number' => 'REC-005',
                'letter_number' => 'LTR-005',
                'period' => '2024-05',
                'letter_subject' => 'Tagihan Mei',
                'category' => 'management_non_fee',
                'status' => 100,
                'last_reviewers' => 'CEO',
                'is_active' => true,
                'created_by' => 5,
                'expired_at' => Carbon::parse('2024-06-01 00:01:00'),
                'contract_number' => 'KPU-005/2024',
                'employer_name' => 'PT. Mega Karya',
                'total' => 21000000
            ],
            (object) [
                'id' => 6,
                'contract_id' => 106,
                'invoice_number' => 'INV-006',
                'receipt_number' => 'REC-006',
                'letter_number' => 'LTR-006',
                'period' => '2024-06',
                'letter_subject' => 'Tagihan Juni',
                'category' => 'management_non_fee',
                'status' => 99,
                'last_reviewers' => null,
                'is_active' => false,
                'created_by' => 6,
                'expired_at' => Carbon::parse('2024-07-01 00:01:00'),
                'contract_number' => 'KPU-004/2024',
                'employer_name' => 'UD. Jaya Sentosa',
                'total' => 9500000
            ],
            (object) [
                'id' => 7,
                'contract_id' => 107,
                'invoice_number' => 'INV-007',
                'receipt_number' => 'REC-007',
                'letter_number' => 'LTR-007',
                'period' => '2024-07',
                'letter_subject' => 'Tagihan Juli',
                'category' => 'management_non_fee',
                'status' => 99,
                'last_reviewers' => 'Admin',
                'is_active' => false,
                'created_by' => 7,
                'expired_at' => Carbon::parse('2024-08-01 00:01:00'),
                'contract_number' => 'KPU-005/2024',
                'employer_name' => 'PT. Mega Karya',
                'total' => 21000000
            ],
        ]);

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

        // Hitung jumlah invoice berdasarkan kategori status untuk PIE CHARTS
        $draftCount = $dataInvoices->where('status', 0)->count();
        $onProgressCount = $dataInvoices->whereIn('status', [1, 2, 3, 4, 5, 6, 9])->count();
        $completedCount = $dataInvoices->where('status', 100)->count();
        $rejectedCount = $dataInvoices->where('status', 99)->count();

        $totalInvoices = $draftCount + $onProgressCount + $completedCount + $rejectedCount;


        return view('pages/dashboard/dashboard', compact('dataInvoices', 'dokumenSementara', 'dataInvoicesReal', 'dataInvoicesNonFee', 'draftCount', 'onProgressCount', 'completedCount', 'rejectedCount', 'totalInvoices'));
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
