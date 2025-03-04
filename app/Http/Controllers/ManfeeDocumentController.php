<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Contracts;
use App\Models\ManfeeDocument;
use App\Models\DocumentApproval;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Notifications\InvoiceApprovalNotification;
// use App\Models\MasterBillType;
use App\Exports\ManfeeDocumentExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ManfeeDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages/ar-menu/management-fee/index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil kontrak yang memiliki type 'management_fee' dan belum memiliki dokumen
        $contracts = Contracts::where('type', 'management_fee')
            ->whereDoesntHave('manfeeDocuments')
            ->with('billTypes')
            ->get();

        // dd($contracts);

        // Format Romawi untuk bulan
        $monthRoman = $this->convertToRoman(date('n'));
        $year = date('Y');

        // Ambil nomor terakhir dan tambahkan 10
        $lastNumber = ManfeeDocument::max('letter_number');
        preg_match('/^(\d{6})/', $lastNumber, $matches);
        $lastNumeric = $matches[1] ?? '000100';
        $nextNumber = $lastNumber ? (intval($lastNumeric) + 10) : 100;

        // Format nomor surat, invoice, dan kwitansi
        $letterNumber = sprintf("%06d/MF/KEU/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $invoiceNumber = sprintf("%06d/MF/KW/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $receiptNumber = sprintf("%06d/MF/INV/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);

        return view('pages/ar-menu/management-fee/create', compact('contracts', 'letterNumber', 'invoiceNumber', 'receiptNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd("Request diterima:", $request->all());

        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'period' => 'required',
            'letter_subject' => 'required',
            'manfee_bill' => 'required',
        ]);

        // Cek apakah contract_id sudah memiliki dokumen management_fee
        $existingDocument = ManfeeDocument::where('contract_id', $request->contract_id)->first();
        if ($existingDocument) {
            return redirect()->back()->withErrors(['contract_id' => 'Dokumen untuk kontrak ini sudah ada.']);
        }


        $monthRoman = $this->convertToRoman(date('n'));
        $year = date('Y');

        // Ambil nomor terakhir dan tambahkan 10
        $lastNumber = ManfeeDocument::max('letter_number');
        preg_match('/^(\d{6})/', $lastNumber, $matches);
        $lastNumeric = $matches[1] ?? '000100';
        $nextNumber = $lastNumber ? (intval($lastNumeric) + 10) : 100;

        $letterNumber = sprintf("%06d/MF/KEU/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $invoiceNumber = sprintf("%06d/MF/KW/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $receiptNumber = sprintf("%06d/MF/INV/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);


        $input = $request->only(['contract_id', 'period', 'letter_subject', 'manfee_bill']);
        $input['letter_number'] = $letterNumber;
        $input['invoice_number'] = $invoiceNumber;
        $input['receipt_number'] = $receiptNumber;
        $input['category'] = 'management_fee';
        $input['status'] = $request->status ?? 0;
        $input['is_active'] = true;
        $input['created_by'] = auth()->id();

        try {
            $manfeeDoc = ManfeeDocument::create($input);

            return redirect()->route('management-fee.edit',  $manfeeDoc)->with('success', 'Data berhasil disimpan!');
        } catch (\Exception $e) {
            // dd($e);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $manfeeDoc = ManfeeDocument::with([
            'accumulatedCosts',
            'attachments',
            'descriptions',
            'taxFiles',
            'approvals.approver'
        ])->findOrFail($id);

        $subtotals = $manfeeDoc->detailPayments->groupBy('expense_type')->map(function ($items) {
            return $items->sum('nilai_biaya');
        });

        $subtotalBiayaNonPersonil = $manfeeDoc->detailPayments
            ->where('expense_type', 'Biaya Non Personil')
            ->sum('nilai_biaya');

        $latestApprover = DocumentApproval::where('document_id', $id)
            ->with('approver')
            ->latest('updated_at') // Ambil hanya yang paling baru
            ->first();

        $jenis_biaya = ['Biaya Personil', 'Biaya Non Personil', 'Biaya Lembur', 'THR', 'Kompesasi', 'SPPD', 'Add Cost'];

        return view('pages.ar-menu.management-fee.invoice-detail.show', compact('manfeeDoc', 'jenis_biaya', 'latestApprover', 'subtotals', 'subtotalBiayaNonPersonil'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $manfeeDoc = ManfeeDocument::with([
            'accumulatedCosts',
            'attachments',
            'descriptions',
            'taxFiles',
            'detailPayments'
        ])->findOrFail($id);

        $subtotals = $manfeeDoc->detailPayments->groupBy('expense_type')->map(function ($items) {
            return $items->sum('nilai_biaya');
        });

        $subtotalBiayaNonPersonil = $manfeeDoc->detailPayments
            ->where('expense_type', 'Biaya Non Personil')
            ->sum('nilai_biaya');

        $rate_manfee = ['9', '10', '11', '12', '13'];
        $jenis_biaya = ['Biaya Personil', 'Biaya Non Personil', 'Biaya Lembur', 'THR', 'Kompesasi', 'SPPD', 'Add Cost'];
        $account_dummy = ['10011', '10012', '10013', '10014', '10015'];

        return view('pages.ar-menu.management-fee.invoice-detail.edit', compact('manfeeDoc', 'jenis_biaya', 'account_dummy', 'subtotals', 'subtotalBiayaNonPersonil', 'rate_manfee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ManfeeDocument $manfeeDocument)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $manfeeDocument = ManfeeDocument::find($id);
        $manfeeDocument->delete();

        return redirect()->route('management-fee.index')->with('success', 'Data berhasil dihapus!');
    }


    private function convertToRoman($month)
    {
        $romans = ["I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"];
        return $romans[$month - 1];
    }

    // Button Approval
    public function processApproval($documentId)
    {
        DB::beginTransaction(); // Memulai transaksi database

        try {
            $document = ManfeeDocument::findOrFail($documentId);
            $currentRole = optional($document->latestApproval)->role ?? 'maker';

            // 🔹 1️⃣ Validasi: Apakah dokumen sudah di tahap akhir approval?
            if ($document->last_reviewers === 'pajak') {
                return back()->with('info', "Dokumen ini sudah berada di tahap akhir approval.");
            }

            // 🔹 2️⃣ Validasi: Apakah user memiliki role yang diizinkan?
            $userRole = Auth::user()->role;

            if (!$userRole || $userRole !== $currentRole) {
                return back()->with('error', "Anda tidak memiliki izin untuk menyetujui dokumen ini.");
            }

            // 🔹 3️⃣ Validasi: Apakah user sudah pernah approve dokumen ini sebelumnya?
            $alreadyApproved = DocumentApproval::where([
                'document_id'   => $document->id,
                'document_type' => ManfeeDocument::class,
                'approver_id'   => Auth::id(),
            ])->exists();

            if ($alreadyApproved) {
                return back()->with('error', "Anda sudah menyetujui dokumen ini sebelumnya.");
            }

            // 🔹 4️⃣ Dapatkan role approval berikutnya
            $nextRole = $this->getNextApprovalRole($currentRole);

            if (!$nextRole) {
                return back()->with('info', "Dokumen ini sudah berada di tahap akhir approval.");
            }

            // 🔹 5️⃣ Ambil user dengan role berikutnya
            $nextApprovers = User::where('role', $nextRole)->get();

            if ($nextApprovers->isEmpty()) {
                return back()->with('error', "Tidak ada user dengan role {$nextRole} yang bisa menyetujui dokumen ini.");
            }

            // 🔹 6️⃣ Simpan approval ke tabel `document_approvals`
            DocumentApproval::create([
                'document_id'   => $document->id,
                'document_type' => ManfeeDocument::class,
                'approver_id'   => Auth::id(),
                'role'          => $currentRole,
                'status'        => (string) array_search($currentRole, $this->approvalStatusMap()), // Sesuaikan status berdasarkan role
                'approved_at'   => now(),
            ]);

            // 🔹 7️⃣ Perbarui reviewer terakhir di dokumen
            $document->update([
                'last_reviewers' => $nextRole,
                'status'         => (string) array_search($nextRole, $this->approvalStatusMap()), // Update status berdasarkan role
            ]);

            // 🔹 8️⃣ Kirim Notifikasi ke Role Berikutnya
            $notification = Notification::create([
                'type'            => InvoiceApprovalNotification::class,
                'notifiable_type' => ManfeeDocument::class,
                'notifiable_id'   => $document->id,
                'data'            => json_encode([
                    'document_id'    => $document->id,
                    'invoice_number' => $document->invoice_number,
                    'action'         => 'approved',
                    'message'        => "Invoice #{$document->invoice_number} membutuhkan persetujuan dari {$nextRole}.",
                    'url'            => route('non-management-fee.show', $document->id),
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 🔹 9️⃣ Kirim notifikasi ke setiap user dengan role berikutnya
            foreach ($nextApprovers as $user) {
                NotificationRecipient::create([
                    'notification_id' => $notification->id,
                    'user_id'         => $user->id,
                    'read_at'         => null,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            DB::commit(); // Simpan semua perubahan dalam transaksi

            return back()->with('success', "Dokumen telah disetujui dan diteruskan ke {$nextRole}.");
        } catch (\Exception $e) {
            DB::rollBack(); // Jika ada error, batalkan semua perubahan
            Log::error("Error saat approval dokumen: " . $e->getMessage());
            return back()->with('error', "Terjadi kesalahan saat memproses approval.");
        }
    }

    /**
     * Fungsi untuk mendapatkan role berikutnya dalam flowchart.
     */
    private function getNextApprovalRole($currentRole)
    {
        $flow = [
            'maker'           => 'kadiv',
            'kadiv'           => 'bendahara',
            'bendahara'       => 'manager_anggaran',
            'manager_anggaran' => 'direktur_keuangan',
            'direktur_keuangan' => 'pajak',
        ];

        return $flow[$currentRole] ?? null;
    }

    /**
     * Mapping Status Approval dengan angka
     */
    private function approvalStatusMap()
    {
        return [
            '0' => 'draft',
            '1' => 'kadiv',
            '2' => 'bendahara',
            '3' => 'manager_anggaran',
            '4' => 'direktur_keuangan',
            '5' => 'pajak',
            '9' => 'need_info',
            '99' => 'rejected',
        ];
    }

    // excel
    public function export(Request $request)
    {
        $ids = $request->query('ids');

        if (!$ids) {
            return back()->with('error', 'Tidak ada data yang dipilih untuk diexport.');
        }

        return Excel::download(new ManfeeDocumentExport($ids), 'manfee_documents.xlsx');
    }
}
