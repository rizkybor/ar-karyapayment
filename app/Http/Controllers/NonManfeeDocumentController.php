<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;

use App\Models\User;
use App\Models\Contracts;
use App\Models\Notification;
use App\Models\DocumentApproval;
use App\Models\NonManfeeDocument;
use App\Models\NonManfeeDocHistory;
use App\Models\NotificationRecipient;
use App\Models\NonManfeeDocAccumulatedCost;

use App\Exports\NonManfeeDocumentExport;
use App\Services\AccurateTransactionService;
use App\Services\AccurateMasterOptionService;
use App\Notifications\InvoiceApprovalNotification;


class NonManfeeDocumentController extends Controller
{
    private AccurateTransactionService $accurateService;
    private AccurateMasterOptionService $accurateOption;

    public function __construct(
        AccurateTransactionService $accurateService,
        AccurateMasterOptionService $accurateOption
    ) {
        $this->accurateService = $accurateService;
        $this->accurateOption = $accurateOption;
    }
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
        $lastNumber = NonManfeeDocument::orderByRaw('CAST(SUBSTRING(letter_number, 1, 6) AS UNSIGNED) DESC')
            ->value('letter_number');


        if (!$lastNumber) {
            $lastNumeric = 100;
        } else {

            preg_match('/^(\d{6})/', $lastNumber, $matches);
            $lastNumeric = $matches[1] ?? '000100';
            $lastNumeric = intval($lastNumeric);

            if ($lastNumeric % 10 !== 0) {
                $lastNumeric = ceil($lastNumeric / 10) * 10;
            }
        }

        $nextNumber = $lastNumeric + 10;

        $letterNumber = sprintf("%06d/NMF/KEU/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $invoiceNumber = sprintf("%06d/NMF/INV/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $receiptNumber = sprintf("%06d/NMF/KW/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);

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
        // $existingDocument = NonManfeeDocument::where('contract_id', $request->contract_id)->first();
        // if ($existingDocument) {
        //     return redirect()->back()->withErrors(['contract_id' => 'Dokumen untuk kontrak ini sudah ada.']);
        // }

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
        $input['expired_at'] = Carbon::now()->addDays(30)->setTime(0, 1, 0);

        try {
            // Simpan dokumen baru
            $document = NonManfeeDocument::create($input);

            // **Buat data di NonManfeeDocAccumulatedCost dengan hanya document_id**
            NonManfeeDocAccumulatedCost::create([
                'document_id' => $document->id,
                'dpp' => '0'
            ]);

            // Redirect ke halaman detail dengan ID yang benar
            return redirect()->route('non-management-fee.edit', ['id' => $document->id])
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

        // jika mau digunakan
        // $dataContract =  $contracts = Contracts::where('id', $nonManfeeDocument->id)
        // ->get();

        $latestApprover = DocumentApproval::where('document_id', $id)
            ->with('approver')
            ->latest('updated_at')
            ->first();

        // 🚀 **Gunakan DropboxController untuk mendapatkan URL file**
        $dropboxController = new DropboxController();

        $dropboxFolderName = '/attachments/';
        foreach ($nonManfeeDocument->attachments as $attachment) {
            $attachment->path = $dropboxController->getAttachmentUrl($attachment->path, $dropboxFolderName);
        }

        $dropboxFolderName = '/taxes/';
        foreach ($nonManfeeDocument->taxFiles as $taxFile) {
            $taxFile->path = $dropboxController->getAttachmentUrl($taxFile->path, $dropboxFolderName);
        }

        // 🚀 **Gunakan Accurate Service untuk mendapatkan URL file**
        $apiResponse = $this->accurateOption->getInventoryList();
        $optionAccount = json_decode($apiResponse, true)['d'];

        return view('pages/ar-menu/non-management-fee/invoice-detail/show', compact(
            'nonManfeeDocument',
            'latestApprover',
            'optionAccount',
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

        // 🚀 **Gunakan DropboxController untuk mendapatkan URL file**
        $dropboxController = new DropboxController();

        $dropboxFolderName = '/attachments/';
        foreach ($nonManfeeDocument->attachments as $attachment) {
            $attachment->path = $dropboxController->getAttachmentUrl($attachment->path, $dropboxFolderName);
        }

        $dropboxFolderName = '/taxes/';
        foreach ($nonManfeeDocument->taxFiles as $taxFile) {
            $taxFile->path = $dropboxController->getAttachmentUrl($taxFile->path, $dropboxFolderName);
        }

        // 🚀 **Gunakan Accurate Service untuk mendapatkan URL file**
        $apiResponse = $this->accurateOption->getInventoryList();
        $optionAccount = json_decode($apiResponse, true)['d'];

        return view('pages/ar-menu/non-management-fee/invoice-detail/edit', compact(
            'nonManfeeDocument',
            'optionAccount'
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
    // public function processApproval(Request $request, $id)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $document = NonManfeeDocument::findOrFail($id);
    //         $user = Auth::user();
    //         $userRole = $user->role;
    //         $department = $user->department;
    //         $previousStatus = $document->status;
    //         $currentRole = optional($document->latestApproval)->approver_role ?? 'maker';
    //         $message = $request->input('messages');

    //         // 🔹 1️⃣ Cek apakah dokumen dalam status revisi
    //         $isRevised = $document->status === '102';

    //         // 🔹 2️⃣  Jika revisi, lewati validasi karena `userRole` dan `currentRole` pasti berbeda
    //         if (!$isRevised && (!$userRole || $userRole !== $currentRole)) {
    //             return back()->with('error', "Anda tidak memiliki izin untuk menyetujui dokumen ini.");
    //         }

    //         $nextRole = null;
    //         $nextApprovers = collect();

    //         if ($document->last_reviewers === 'pajak') {
    //             // 🔹 3️⃣ Jika reviewer terakhir adalah 'pajak', kirim kembali ke 'pembendaharaan'
    //             $nextRole = 'pembendaharaan';
    //             $statusCode = '6';
    //             $nextApprovers = User::where('role', $nextRole)->get();
    //         }
    //         // 🔹 4️⃣ Jika revisi, kembalikan ke approver sebelumnya
    //         elseif ($isRevised) {
    //             // 🔹 4️⃣ Ambil APPROVER TERAKHIR secara keseluruhan
    //             $lastApprover = DocumentApproval::where('document_id', $document->id)
    //                 ->where('document_type', NonManfeeDocument::class)
    //                 ->latest('approved_at') // Urutkan berdasarkan waktu approval terbaru
    //                 ->first();

    //             if (!$lastApprover) {
    //                 return back()->with('error', "Gagal mengembalikan dokumen revisi: Approver sebelumnya tidak ditemukan.");
    //             }

    //             $nextRole = $lastApprover->approver_role;
    //             $nextApprovers = User::where('role', $nextRole)->get();
    //         } else {
    //             // 🔹 5️⃣ Jika bukan revisi, tentukan ROLE BERIKUTNYA seperti biasa
    //             $nextRole = $this->getNextApprovalRole($currentRole, $department, $isRevised);
    //             if (!$nextRole) {
    //                 return back()->with('info', "Dokumen ini sudah berada di tahap akhir approval.");
    //             }

    //             // 🔹 6️⃣ Ambil user dengan role berikutnya
    //             $nextApprovers = User::where('role', $nextRole)
    //                 ->when($nextRole === 'kadiv', function ($query) use ($department) {
    //                     return $query->whereRaw("LOWER(department) = ?", [strtolower($department)]);
    //                 })
    //                 ->get();
    //         }

    //         if ($nextApprovers->isEmpty()) {
    //             Log::warning("Approval gagal: Tidak ada user dengan role {$nextRole} untuk dokumen ID {$document->id}");
    //             return back()->with('error', "Tidak ada user dengan role {$nextRole}" .
    //                 ($nextRole === 'kadiv' ? " di departemen {$department}." : "."));
    //         }

    //         // 🔹 7️⃣ Ambil status dokumen berdasarkan nextRole
    //         $statusCode = array_search($nextRole, $this->approvalStatusMap());

    //         if ($statusCode === false) {
    //             Log::warning("Approval Status Map tidak mengenali role: {$nextRole}");
    //             $statusCode = 'unknown';
    //         }

    //         // 🔹 8️⃣ Simpan approval untuk user berikutnya
    //         foreach ($nextApprovers as $nextApprover) {
    //             DocumentApproval::create([
    //                 'document_id'    => $document->id,
    //                 'document_type'  => NonManfeeDocument::class,
    //                 'approver_id'    => $nextApprover->id,
    //                 'approver_role'  => $nextRole,
    //                 'submitter_id'   => $document->created_by,
    //                 'submitter_role' => $userRole,
    //                 'status'         => (string) $statusCode,
    //                 'approved_at'    => now(),
    //             ]);
    //         }

    //         // 🔹 9️⃣ Perbarui status dokumen
    //         $document->update([
    //             'last_reviewers' => $nextRole,
    //             'status'         => (string) $statusCode,
    //         ]);

    //         // 🔹 🔟 Simpan ke History
    //         NonManfeeDocHistory::create([
    //             'document_id'     => $document->id,
    //             'performed_by'    => $user->id,
    //             'role'            => $userRole,
    //             'previous_status' => $previousStatus,
    //             'new_status'      => (string) $statusCode,
    //             'action'          => $isRevised ? 'Revised Approval' : 'Approved',
    //             'notes'           => $message ? "{$message}." : "Dokumen diproses oleh {$user->name}.",
    //         ]);

    //         // 🔹 🔟 Kirim Notifikasi
    //         $notification = Notification::create([
    //             'type'            => InvoiceApprovalNotification::class,
    //             'notifiable_type' => NonManfeeDocument::class,
    //             'notifiable_id'   => $document->id,
    //             'messages'        => $message
    //                 ? "{$message}. Lihat detail: " . route('non-management-fee.show', $document->id)
    //                 : "Dokumen telah disetujui oleh {$user->name}. Lihat detail: " . route('non-management-fee.show', $document->id),
    //             'sender_id'       => $user->id,
    //             'sender_role'     => $userRole,
    //             'read_at'         => null,
    //             'created_at'      => now(),
    //             'updated_at'      => now(),
    //         ]);

    //         // 🔹 🔟 Kirim notifikasi ke setiap user dengan role berikutnya
    //         foreach ($nextApprovers as $nextApprover) {
    //             NotificationRecipient::create([
    //                 'notification_id' => $notification->id,
    //                 'user_id'         => $nextApprover->id,
    //                 'read_at'         => null,
    //                 'created_at'      => now(),
    //                 'updated_at'      => now(),
    //             ]);
    //         }

    //         DB::commit();

    //         return back()->with('success', "Dokumen telah " . ($isRevised ? "dikembalikan ke {$nextRole} sebagai revisi" : "disetujui dan diteruskan ke {$nextRole}."));
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error("Error saat approval dokumen [ID: {$id}]: " . $e->getMessage());
    //         return back()->with('error', "Terjadi kesalahan saat memproses approval.");
    //     }
    // }

    public function processApproval(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // 🔍 Ambil dokumen berdasarkan ID
            $document = NonManfeeDocument::with(['attachments', 'accumulatedCosts'])->findOrFail($id);

            // ✅ Cek apakah ada lampiran (attachments)
            if ($document->attachments->isEmpty()) {
                return back()->with(
                    'error',
                    "Dokumen tidak dapat diproses karena belum memiliki lampiran."
                );
            }

            // ✅ Cek apakah ada akumulasi biaya (accumulatedCosts)
            if ($document->accumulatedCosts->pluck('account')[0] == null) {
                return back()->with(
                    'error',
                    "Dokumen tidak dapat diproses karena tidak ada akun yang terpilih pada akumulasi biaya."
                );
            }

            $document = NonManfeeDocument::findOrFail($id);
            $user = Auth::user();
            $userRole = $user->role;
            $department = $user->department;
            $previousStatus = $document->status;
            $currentRole = optional($document->latestApproval)->approver_role ?? 'maker';
            $message = $request->input('messages');

            // 🔹 1️⃣ Cek apakah dokumen dalam status revisi
            $isRevised = $document->status === '102';

            // 🔹 2️⃣ Validasi izin approval
            if (!$isRevised && (!$userRole || $userRole !== $currentRole)) {
                return back()->with('error', "Anda tidak memiliki izin untuk menyetujui dokumen ini.");
            }

            $nextRole = null;
            $nextApprovers = collect();

            // 🔹 3️⃣ Jika reviewer terakhir adalah 'pajak', kirim kembali ke 'pembendaharaan'
            if ($document->last_reviewers === 'pajak') {
                // ✅ Cek apakah ada faktur pajak (tax files)
                if ($document->taxFiles->isEmpty()) {
                    return back()->with(
                        'error',
                        "Faktur pajak belum ada, upload faktur pajak dahulu sebelum anda melakukan approval"
                    );
                }

                // Assign Privy Services
                // Assign Accurate Services

                $nextRole = 'pembendaharaan';
                $statusCode = '6'; // done
                $nextApprovers = User::where('role', $nextRole)->get();
            }
            // 🔹 4️⃣ Jika revisi, kembalikan ke approver sebelumnya
            elseif ($isRevised) {
                $lastApprover = DocumentApproval::where('document_id', $document->id)
                    ->where('document_type', NonManfeeDocument::class)
                    ->latest('approved_at')
                    ->first();

                if (!$lastApprover) {
                    return back()->with('error', "Gagal mengembalikan dokumen revisi: Approver sebelumnya tidak ditemukan.");
                }
                $nextRole = $lastApprover->approver_role;
                $statusCode = $lastApprover->status;

                $nextApprovers = User::where('id', $lastApprover->approver_id)->get();
            }
            // 🔹 5️⃣ Jika bukan revisi & bukan pajak, lanjutkan approval normal
            else {

                $nextRole = $this->getNextApprovalRole($currentRole, $department, $isRevised);
                if (!$nextRole) {
                    return back()->with('info', "Dokumen ini sudah berada di tahap akhir approval.");
                }

                $nextApprovers = User::where('role', $nextRole)
                    ->when($nextRole === 'kadiv', function ($query) use ($department) {
                        return $query->whereRaw("LOWER(department) = ?", [strtolower($department)]);
                    })
                    ->get();

                $statusCode = array_search($nextRole, $this->approvalStatusMap());
                if ($statusCode === false) {
                    Log::warning("Approval Status Map tidak mengenali role: {$nextRole}");
                    $statusCode = 'unknown';
                }
            }

            // 🔹 6️⃣ Jika tidak ada user untuk role berikutnya, batalkan
            if ($nextApprovers->isEmpty()) {
                Log::warning("Approval gagal: Tidak ada user dengan role {$nextRole} untuk dokumen ID {$document->id}");
                return back()->with('error', "Tidak ada user dengan role {$nextRole}" .
                    ($nextRole === 'kadiv' ? " di departemen {$department}." : "."));
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
                'action'          => $isRevised ? 'Revised Approval' : 'Approved',
                'notes'           => $message ? "{$message}." : "Dokumen diproses oleh {$user->name}.",
            ]);

            // 🔹 🔟 Kirim Notifikasi
            $notification = Notification::create([
                'type'            => InvoiceApprovalNotification::class,
                'notifiable_type' => NonManfeeDocument::class,
                'notifiable_id'   => $document->id,
                'messages'        => $message
                    ? "{$message}. Lihat detail: " . route('non-management-fee.show', $document->id)
                    : "Dokumen telah disetujui oleh {$user->name}. Lihat detail: " . route('non-management-fee.show', $document->id),
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

            return back()->with('success', "Dokumen telah " . ($isRevised ? "dikembalikan ke {$nextRole} sebagai revisi" : "disetujui dan diteruskan ke {$nextRole}."));
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
            '6'   => 'done',
            '100' => 'finished', // status belum digunakan
            '101' => 'canceled', // status belum digunakan
            '102' => 'revised',
            '103'  => 'rejected',
        ];
    }

    /**
     * Untuk button revision
     */
    public function processRevision(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $document = NonManfeeDocument::findOrFail($id);
            $user = Auth::user();
            $userRole = $user->role;
            $currentRole = $document->latestApproval->approver_role ?? 'maker';
            $message = $request->input('messages');

            // 🔹 1️⃣ Validasi: Pastikan user memiliki hak revisi
            if ($userRole !== $currentRole) {
                return back()->with('error', "Anda tidak memiliki izin untuk merevisi dokumen ini.");
            }

            // 🔹 2️⃣ Ambil Approver Terakhir yang Merevisi Sebagai Target Approver
            $lastReviser = DocumentApproval::where('document_id', $document->id)
                ->where('document_type', NonManfeeDocument::class)
                ->where('status', '102') // Ambil approval yang terakhir kali merevisi
                ->latest('approved_at')
                ->first();

            $targetApproverId = $lastReviser->approver_id ?? $document->created_by;
            $targetApprover = User::find($targetApproverId);
            $targetApproverRole = $targetApprover->role ?? 'maker';



            // 🔹 4️⃣ Simpan revisi ke dalam log approval (Pastikan tidak ada duplikasi)
            DocumentApproval::updateOrCreate(
                [
                    'document_id'   => $document->id,
                    'document_type' => NonManfeeDocument::class,
                    'approver_id'   => $user->id,
                ],
                [
                    'role'         => $userRole,
                    'status'       => $document->status,
                    'approved_at'  => now(),
                ]
            );

            // 🔹 3️⃣ Update status dokumen menjadi "Revisi Selesai (102)" dan set approver terakhir
            $document->update([
                'status'         => '102',
                'last_reviewers' => $userRole,
            ]);

            // 🔹 5️⃣ Simpan riwayat revisi di `NonManfeeDocHistory`
            NonManfeeDocHistory::create([
                'document_id'     => $document->id,
                'performed_by'    => $user->id,
                'role'            => $userRole,
                'previous_status' => $document->status,
                'new_status'      => '102',
                'action'          => 'Revised',
                'notes'           => "Dokumen direvisi oleh {$user->name} dan dikembalikan ke {$targetApprover->name}.",
            ]);

            // 🔹 6️⃣ Kirim Notifikasi ke Approver yang Merevisi Sebelumnya
            if ($targetApprover) {
                $notification = Notification::create([
                    'type'            => InvoiceApprovalNotification::class,
                    'notifiable_type' => NonManfeeDocument::class,
                    'notifiable_id'   => $document->id,
                    'messages'        =>  $message
                        ? "{$message}. Lihat detail: " . route('non-management-fee.show', $document->id)
                        : "Dokumen diproses oleh {$user->name}.",
                    'sender_id'       => $user->id,
                    'sender_role'     => $userRole,
                    'read_at'         => null,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                // 🔹 7️⃣ Tambahkan ke Notifikasi Recipient
                NotificationRecipient::create([
                    'notification_id' => $notification->id,
                    'user_id'         => $targetApprover->id,
                    'read_at'         => null,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            DB::commit();
            return back()->with('success', "Dokumen telah dikembalikan ke {$targetApprover->name} untuk pengecekan ulang.");
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

    public function rejected(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $document = NonManfeeDocument::findOrFail($id);

        // Ambil file dan nama untuk diupload
        $file = $request->file('file');
        $fileName = 'Pembatalan ' . $document->letter_subject;
        $dropboxFolderName = '/rejected/';

        // Upload ke Dropbox
        $dropboxController = new DropboxController();
        $dropboxPath = $dropboxController->uploadAttachment($file, $fileName, $dropboxFolderName);

        if (!$dropboxPath) {
            return back()->with('error', 'Gagal mengunggah file penolakan.');
        }

        // Simpan ke database

        $document->update([
            'reason_rejected' => $request->reason,
            'path_rejected' => $dropboxPath,
            'status' => 103,
        ]);

        return redirect()->route('non-management-fee.show', $document->id)
            ->with('success', 'Dokumen berhasil dibatalkan.');
    }
}
