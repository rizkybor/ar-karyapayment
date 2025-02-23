<?php

namespace App\Http\Controllers;

use App\Models\Contracts;
use App\Models\NonManfeeDocument;
use App\Models\MasterBillType;
use App\Models\User;
use App\Models\ArDocument;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Notifications\InvoiceApprovalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NonManfeeDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $NonManfeeDocs = NonManfeeDocument::with('contract')->get();
        return view('pages/ar-menu/management-non-fee/index', compact('NonManfeeDocs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contracts = Contracts::where('type', 'management_non_fee')
            ->whereDoesntHave('nonManfeeDocuments')
            ->with('billTypes')
            ->get();

        $monthRoman = $this->convertToRoman(date('n'));
        $year = date('Y');

        $lastNumber = NonManfeeDocument::max('letter_number');
        $nextNumber = $lastNumber ? (intval(substr($lastNumber, 4, 6)) + 10) : 100;

        $letterNumber = sprintf("No. %06d/KEU/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $invoiceNumber = sprintf("No. %06d/KW/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $receiptNumber = sprintf("No. %06d/INV/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);

        return view('pages/ar-menu/management-non-fee/create', compact('contracts', 'letterNumber', 'invoiceNumber', 'receiptNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $documentId = 1; // ID Dummy

        return redirect()->route('management-non-fee.show', ['id' => $documentId])
            ->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $document = [
            'id' => $id,
            'letter_number' => 'No. 001/KEU/KPU/SOL/I/2025',
            'invoice_number' => 'No. 001/KW/KPU/SOL/I/2025',
            'receipt_number' => 'No. 001/INV/KPU/SOL/I/2025',
            'contract_id' => 123,
            'period' => 'Januari 2025',
            'letter_subject' => 'Tagihan Jasa Konsultasi',
            'bill_type' => 'Non-Manfee',
            'status' => 'Draft',
            'is_active' => 'True',
            'created_by' => 'Admin',
            'created_at' => now()->format('d M Y H:i'),
        ];

        $attachments = [
            (object) ['id' => 1, 'name' => 'BAP'],
            (object) ['id' => 2, 'name' => 'Invoice'],
            (object) ['id' => 3, 'name' => 'Kontrak Kerja'],
        ];

        $files_faktur = [
            (object) ['id' => 1, 'name' => 'File Faktur Pajak'],
        ];

        return view('pages/ar-menu/management-non-fee/invoice-detail/show', compact('document', 'attachments', 'files_faktur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $document = [
            'id' => $id,
            'letter_number' => 'No. 001/KEU/KPU/SOL/I/2025',
            'invoice_number' => 'No. 001/KW/KPU/SOL/I/2025',
            'receipt_number' => 'No. 001/INV/KPU/SOL/I/2025',
            'contract_id' => 123,
            'period' => 'Januari 2025',
            'letter_subject' => 'Tagihan Jasa Konsultasi',
            'bill_type' => 'Non-Manfee',
            'status' => 'Draft',
            'is_active' => 'True',
            'created_by' => 'Admin',
            'created_at' => now()->format('d M Y H:i'),
        ];

        $attachments = [
            (object) ['id' => 1, 'name' => 'BAP'],
            (object) ['id' => 2, 'name' => 'Invoice'],
            (object) ['id' => 3, 'name' => 'Kontrak Kerja'],
        ];

        $files_faktur = [
            (object) ['id' => 1, 'name' => 'File Faktur Pajak'],
        ];

        return view('pages/ar-menu/management-non-fee/invoice-detail/edit', compact('document', 'attachments', 'files_faktur'));
    }

   /**
     * Menyetujui invoice dan mengirimkan notifikasi ke role berikutnya.
     */
    public function approveInvoice($document_id)
    {
        $document = ArDocument::findOrFail($document_id);
        
        // Ambil role berikutnya dalam flowchart
        $nextRole = $this->getNextApprovalRole($document);

        if ($nextRole) {
            // Ambil semua user dengan role berikutnya
            $users = User::where('role', $nextRole)->get();

            if ($users->count() > 0) {
                // Simpan notifikasi ke dalam tabel notifications
                $notification = Notification::create([
                    'id' => Str::uuid(),
                    'type' => InvoiceApprovalNotification::class,
                    'data' => json_encode([
                        'document_id' => $document->id,
                        'invoice_number' => $document->invoice_number,
                        'action' => 'approved',
                        'message' => "Invoice #{$document->invoice_number} membutuhkan persetujuan dari {$nextRole}.",
                        'url' => route('management-non-fee.show', $document->id),
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Simpan setiap user yang menerima notifikasi ke tabel pivot
                foreach ($users as $user) {
                    NotificationRecipient::create([
                        'id' => Str::uuid(),
                        'notification_id' => $notification->id,
                        'recipient_id' => $user->id,
                        'read_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        return back()->with('success', 'Invoice telah disetujui dan notifikasi telah dikirim.');
    }

    /**
     * Fungsi untuk mendapatkan role berikutnya dalam flowchart.
     */
    private function getNextApprovalRole($document)
    {
        $approvalSteps = [
            'Maker' => 'Kepala Divisi',
            'Kepala Divisi' => 'Pembendahara Raan',
            'Pembendahara Raan' => 'Manager Keuangan',
            'Manager Keuangan' => 'Direktur Keuangan',
            'Direktur Keuangan' => 'Pajak',
        ];

        return $approvalSteps[$document->last_reviewers] ?? null;
    }

    /**
     * Fungsi untuk mengubah angka bulan menjadi format romawi.
     */
    private function convertToRoman($month)
    {
        $romans = ["I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"];
        return $romans[$month - 1];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $NonManfeeDocument = NonManfeeDocument::find($id);
        $NonManfeeDocument->delete();

        return redirect()->route('management-fee.index')->with('success', 'Data berhasil dihapus!');
    }

    public function attachments($id)
    {
        $attachments = [
            (object) ['id' => 1, 'name' => 'BAP'],
            (object) ['id' => 2, 'name' => 'Invoice'],
            (object) ['id' => 3, 'name' => 'Kontrak Kerja'],
        ];

        return view('pages/ar-menu/management-non-fee/invoice-detail/show', compact('attachments'));
    }

    public function viewAttachment($id)
    {
        return response()->json(['message' => "Melihat Lampiran dengan ID: $id"]);
    }

    public function destroyAttachment($id)
    {
        return redirect()->back()->with('success', "Lampiran dengan ID: $id telah dihapus.");
    }
}