<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Contracts;
use App\Models\NonManfeeDocument;
use App\Models\DocumentApproval;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Notifications\InvoiceApprovalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Exports\NonManfeeDocumentExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class NonManfeeDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $NonManfeeDocs = NonManfeeDocument::with('contract')->get();
        // return view('pages/ar-menu/management-non-fee/index', compact('NonManfeeDocs'));
        // Ambil jumlah per halaman dari query string (default: 10)
        $perPage = $request->input('per_page', 10);

        // Ambil data dengan pagination
        $NonManfeeDocs = NonManfeeDocument::with('contract')->paginate($perPage);

        return view('pages.ar-menu.management-non-fee.index', compact('NonManfeeDocs', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contracts = Contracts::where('type', 'non_management_fee')
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
        // Validasi input
        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'period' => 'required',
            'letter_subject' => 'required',
        ]);

        // Cek apakah contract_id sudah memiliki dokumen non_manfee
        $existingDocument = NonManfeeDocument::where('contract_id', $request->contract_id)->first();
        if ($existingDocument) {
            return redirect()->back()->withErrors(['contract_id' => 'Dokumen untuk kontrak ini sudah ada.']);
        }

        // Generate nomor surat, invoice, dan kwitansi
        $monthRoman = $this->convertToRoman(date('n'));
        $year = date('Y');

        $lastNumber = NonManfeeDocument::max('letter_number');
        $nextNumber = $lastNumber ? (intval(substr($lastNumber, 4, 6)) + 10) : 100;

        $letterNumber = sprintf("No. %06d/KEU/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $invoiceNumber = sprintf("No. %06d/KW/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $receiptNumber = sprintf("No. %06d/INV/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);

        // Menyiapkan data untuk disimpan
        $input = $request->all();
        $input['letter_number'] = $letterNumber;
        $input['invoice_number'] = $invoiceNumber;
        $input['receipt_number'] = $receiptNumber;
        $input['category'] = 'management_non_fee';
        $input['status'] = $input['status'] ?? 0;
        $input['is_active'] = true;
        $input['created_by'] = auth()->id();

        try {
            // Simpan dokumen baru
            $document = NonManfeeDocument::create($input);

            // Redirect ke halaman detail dengan ID yang benar
            return redirect()->route('management-non-fee.show', ['id' => $document->id])
                ->with('success', 'Data berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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

    public function processApproval($documentId)
    {
        DB::beginTransaction(); // Memulai transaksi database

        try {
            $document = NonManfeeDocument::findOrFail($documentId);
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
                'document_type' => NonManfeeDocument::class,
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
                'document_type' => NonManfeeDocument::class,
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
                'notifiable_type' => NonManfeeDocument::class,  
                'notifiable_id'   => $document->id,             
                'data'            => json_encode([
                    'document_id'    => $document->id,
                    'invoice_number' => $document->invoice_number,
                    'action'         => 'approved',
                    'message'        => "Invoice #{$document->invoice_number} membutuhkan persetujuan dari {$nextRole}.",
                    'url'            => route('management-non-fee.show', $document->id),
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

        return redirect()->route('management-non-fee.index')->with('success', 'Data berhasil dihapus!');
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

    public function export(Request $request)
    {
        $ids = $request->query('ids');

        if (!$ids) {
            return back()->with('error', 'Tidak ada data yang dipilih untuk diexport.');
        }

        return Excel::download(new NonManfeeDocumentExport($ids), 'non_manfee_documents.xlsx');
    }
}
