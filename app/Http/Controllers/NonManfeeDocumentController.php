<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Contracts;
// use Yajra\DataTables\DataTables;
use Yajra\DataTables\Facades\DataTables;
use App\Models\NonManfeeDocument;
use App\Models\NonManfeeDocAccumulatedCost;
use App\Models\NonManfeeDocHistory;
use App\Models\DocumentApproval;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Notifications\InvoiceApprovalNotification;
use Illuminate\Http\Request;
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
        return view('pages/ar-menu/non-management-fee/index');
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

        // Ambil nomor terakhir dan tambahkan 10
        $lastNumber = NonManfeeDocument::max('letter_number');
        preg_match('/^(\d{6})/', $lastNumber, $matches);
        $lastNumeric = $matches[1] ?? '000100';
        $nextNumber = $lastNumber ? (intval($lastNumeric) + 10) : 100;

        $letterNumber = sprintf("No. %06d/NF/KEU/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $invoiceNumber = sprintf("No. %06d/NF/KW/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $receiptNumber = sprintf("No. %06d/NF/INV/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);

        return view('pages/ar-menu/non-management-fee/create', compact('contracts', 'letterNumber', 'invoiceNumber', 'receiptNumber'));
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

        // Ambil nomor terakhir dan tambahkan 10
        $lastNumber = NonManfeeDocument::max('letter_number');
        preg_match('/^(\d{6})/', $lastNumber, $matches);
        $lastNumeric = $matches[1] ?? '000100';
        $nextNumber = $lastNumber ? (intval($lastNumeric) + 10) : 100;

        $letterNumber = sprintf("%06d/NF/KEU/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $invoiceNumber = sprintf("%06d/NF/KW/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $receiptNumber = sprintf("%06d/NF/INV/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);

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

            // **Buat data di NonManfeeDocAccumulatedCost dengan hanya document_id**
            NonManfeeDocAccumulatedCost::create([
                'document_id' => $document->id,
            ]);

            // Redirect ke halaman detail dengan ID yang benar
            return redirect()->route('non-management-fee.show', ['id' => $document->id])
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
        // Ambil data Non Manfee Document berdasarkan ID
        $nonManfeeDocument = NonManfeeDocument::with([
            'accumulatedCosts',
            'attachments',
            'descriptions',
            'taxFiles',
            'approvals.approver'
        ])->findOrFail($id);

        $latestApprover = DocumentApproval::where('document_id', $id)
            ->with('approver')
            ->latest('updated_at') // Ambil hanya yang paling baru
            ->first();

            // dd($latestApprover);
        return view('pages/ar-menu/non-management-fee/invoice-detail/show', compact(
            'nonManfeeDocument',
            'latestApprover'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Ambil data Non Manfee Document berdasarkan ID dengan relasi
        $nonManfeeDocument = NonManfeeDocument::with([
            'accumulatedCosts',
            'attachments',
            'descriptions',
            'taxFiles'
        ])->findOrFail($id);

        $akunOptions = ['Kas (0001)', 'Bank (0002)', 'Piutang (0003)', 'Hutang (0004)', 'Modal (0005)'];

        return view('pages/ar-menu/non-management-fee/invoice-detail/edit', compact(
            'nonManfeeDocument',
            'akunOptions'
        ));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $nonManfeeDocument = NonManfeeDocument::find($id);
        $nonManfeeDocument->delete();

        return redirect()->route('non-management-fee.index')->with('success', 'Data berhasil dihapus!');
    }

    /**
     * Proses Document with Approval Level
     */
    public function processApproval(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $document = NonManfeeDocument::findOrFail($id);
            $user = Auth::user();
            $userRole = $user->role;
            $department = $user->department;
            $previousStatus = $document->status;
            $currentRole = optional($document->latestApproval)->approver_role ?? 'maker';
            $message = $request->input('messages');

            // 🔹 1️⃣ Validasi: Apakah dokumen sudah di tahap akhir approval?
            if ($document->last_reviewers === 'pajak') {
                return back()->with('info', "Dokumen ini sudah berada di tahap akhir approval.");
            }

            // 🔹 2️⃣ Validasi: Apakah user memiliki izin approval?
            if (!$userRole || $userRole !== $currentRole) {
                return back()->with('error', "Anda tidak memiliki izin untuk menyetujui dokumen ini.");
            }

            // 🔹 4️⃣ Cek apakah dokumen dalam status revisi
            $isRevised = $document->status === '101';

            // 🔹 5️⃣ Tentukan role berikutnya
            $nextRole = $this->getNextApprovalRole($currentRole, $department, $isRevised);

            if (!$nextRole) {
                return back()->with('info', "Dokumen ini sudah berada di tahap akhir approval.");
            }


            // 🔹 6️⃣ Ambil user dengan role berikutnya
            $nextApprovers = User::where('role', $nextRole)
                ->when($nextRole === 'kadiv', function ($query) use ($department) {
                    return $query->whereRaw("LOWER(department) = ?", [strtolower($department)]);
                })
                ->get();

            if ($nextApprovers->isEmpty()) {
                Log::warning("Approval gagal: Tidak ada user dengan role {$nextRole} untuk dokumen ID {$document->id}");
                return back()->with('error', "Tidak ada user dengan role {$nextRole}" .
                    ($nextRole === 'kadiv' ? " di departemen {$department}." : "."));
            }

            // Input status berikutnya untuk dokumen
            $statusCode = array_search($nextRole, $this->approvalStatusMap());
            
            if ($statusCode === false) {
                Log::warning("Approval Status Map tidak mengenali role: {$nextRole}");
                $statusCode = 'unknown';
            }

            // 🔹 7️⃣ Simpan approval untuk user berikutnya
            foreach ($nextApprovers as $nextApprover) {
                DocumentApproval::create([
                    'document_id'    => $document->id,
                    'document_type'  => NonManfeeDocument::class,
                    'approver_id'    => $nextApprover->id,
                    'approver_role'  => $nextRole,
                    'submitter_id'   => $document->created_by,
                    'submitter_role' => $userRole,
                    'status'         => (string) $statusCode,
                    'approved_at'    => now(),
                ]);
            }

            // 🔹 8️⃣ Perbarui status dokumen
            $document->update([
                'last_reviewers' => $nextRole,
                'status'         => (string) $statusCode,
            ]);

            // 🔹 9️⃣ Simpan ke History
            NonManfeeDocHistory::create([
                'document_id'     => $document->id,
                'performed_by'    => $user->id,
                'role'            => $userRole,
                'previous_status' => $previousStatus,
                'new_status'      => (string) $statusCode,
                'action'          => '',
                'notes'           => $message ? "{$message}." : "Dokumen diproses oleh {$user->name}.",
            ]);

            // 🔹 🔟 Kirim Notifikasi
            $notification = Notification::create([
                'type'            => InvoiceApprovalNotification::class,
                'notifiable_type' => NonManfeeDocument::class,
                'notifiable_id'   => $document->id,
                'messages'        => $message
                    ? "#{$message}. Lihat detail: " . route('non-management-fee.show', $document->id)
                    : "Dokumen diproses oleh {$user->name}.",
                'sender_id'       => $user->id,
                'sender_role'     => $userRole,
                'read_at'         => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // 🔹 🔟 Kirim notifikasi ke setiap user dengan role berikutnya
            foreach ($nextApprovers as $nextApprover) {
                NotificationRecipient::create([
                    'notification_id' => $notification->id,
                    'user_id'         => $nextApprover->id,
                    'read_at'         => null,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            DB::commit();

            return back()->with('success', "Dokumen telah " . ($isRevised ? "direvisi dan dikirim kembali ke {$nextRole}" : "disetujui dan diteruskan ke {$nextRole}."));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error saat approval dokumen [ID: {$id}]: " . $e->getMessage());
            return back()->with('error', "Terjadi kesalahan saat memproses approval.");
        }
    }

    /**
     * Fungsi untuk mendapatkan role berikutnya dalam flowchart.
     */
    private function getNextApprovalRole($currentRole, $department = null, $isRevised = false)
    {
        // Jika dokumen direvisi, kembalikan ke role sebelumnya
        if ($isRevised) {
            return $currentRole; // Kembali ke atasan yang meminta revisi
        }

        // Alur approval normal
        if ($currentRole === 'maker' && $department) {
            return 'kadiv';
        }

        $flow = [
            'kadiv'               => 'pembendaharaan',
            'pembendaharaan'      => 'manager_anggaran',
            'manager_anggaran'    => 'direktur_keuangan',
            'direktur_keuangan'   => 'pajak',
            'pajak'               => 'pembendaharaan'
        ];

        return $flow[$currentRole] ?? null;
    }

    /**
     * Mapping Status Approval dengan angka
     */
    private function approvalStatusMap()
    {
        return [
            '0'   => 'draft',
            '1'   => 'kadiv',
            '2'   => 'pembendaharaan',
            '3'   => 'manager_anggaran',
            '4'   => 'direktur_keuangan',
            '5'   => 'pajak',
            '6'   => 'pembendaharaan',
            '100' => 'completed',
            '101' => 'canceled',
            '102' => 'revised',
            '103'  => 'rejected',
        ];
    }

    /**
     * Untuk button revision
     */
    public function processRevision($id)
    {
        DB::beginTransaction();
        try {

            $document = NonManfeeDocument::findOrFail($id);
            $userRole = Auth::user()->role;
            $currentRole = optional($document->latestApproval)->role ?? 'maker';

            dd($document, $userRole, $currentRole, 'Revision');
            // 🔹 Validasi: Pastikan user memiliki hak revisi
            if ($userRole !== $currentRole) {
                return back()->with('error', "Anda tidak memiliki izin untuk merevisi dokumen ini.");
            }

            // 🔹 Ambil approver terakhir sebelum revisi
            $previousApproval = DocumentApproval::where('document_id', $document->id)
                ->where('document_type', NonManfeeDocument::class)
                ->where('role', '!=', $currentRole)
                ->latest('approved_at')
                ->first();

            if (!$previousApproval) {
                return back()->with('error', "Tidak dapat menentukan siapa yang akan menerima revisi.");
            }

            // 🔹 Kembalikan dokumen ke approver sebelumnya
            $document->update([
                'status'         => '101',
                'last_reviewers' => $previousApproval->role, // Kembali ke yang terakhir approve
            ]);

            // 🔹 Simpan revisi ke dalam log approval
            DocumentApproval::create([
                'document_id'    => $document->id,
                'document_type'  => NonManfeeDocument::class,
                'approver_id'    => Auth::id(),
                'role'           => $userRole,
                'status'         => '101', // Revised
                'approved_at'    => now(),
            ]);

            // 🔹 Kirim Notifikasi ke Approver Sebelumnya
            $previousApprover = User::find($previousApproval->approver_id);
            if ($previousApprover) {
                Notification::create([
                    'type'            => InvoiceApprovalNotification::class,
                    'notifiable_type' => NonManfeeDocument::class,
                    'notifiable_id'   => $document->id,
                    'data'            => json_encode([
                        'id'            => $document->id,
                        'invoice_number' => $document->invoice_number,
                        'action'        => 'revised',
                        'message'       => "Dokumen telah direvisi oleh {$userRole} dan dikembalikan kepada Anda.",
                        'url'           => route('non-management-fee.show', $document->id),
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return back()->with('success', "Dokumen telah dikembalikan ke {$previousApproval->role} untuk revisi.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error saat merevisi dokumen [ID: {$id}]: " . $e->getMessage());
            return back()->with('error', "Terjadi kesalahan saat mengembalikan dokumen untuk revisi.");
        }
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
     * Fungsi untuk Export File Non Manfee
     */
    public function export(Request $request)
    {
        $ids = $request->query('ids');

        if (!$ids) {
            return back()->with('error', 'Tidak ada data yang dipilih untuk diexport.');
        }

        return Excel::download(new NonManfeeDocumentExport($ids), 'non_manfee_documents.xlsx');
    }
}
