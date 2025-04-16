<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Maatwebsite\Excel\Facades\Excel;

use App\Models\User;
use App\Models\Contracts;
use App\Models\ManfeeDocument;
use App\Models\DocumentApproval;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Notifications\InvoiceApprovalNotification;
use App\Models\ManfeeDocHistories;
// use App\Models\MasterBillType;

use App\Exports\ManfeeDocumentExport;
use App\Models\BankAccount;
use App\Services\AccurateTransactionService;
use App\Services\AccurateMasterOptionService;

class ManfeeDocumentController extends Controller
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
    public function index()
    {
        return view('pages/ar-menu/management-fee/index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contracts = Contracts::where('type', 'management_fee')->get();

        $monthRoman = $this->convertToRoman(date('n'));
        $year = date('Y');

        $lastNumber = ManfeeDocument::orderByRaw('CAST(SUBSTRING(letter_number, 1, 6) AS UNSIGNED) DESC')
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

        $monthRoman = $this->convertToRoman(date('n'));
        $year = date('Y');

        $lastNumber = ManfeeDocument::orderByRaw('CAST(SUBSTRING(letter_number, 1, 6) AS UNSIGNED) DESC')
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
        $input['expired_at'] = Carbon::now()->addDays(30)->setTime(0, 1, 0);

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

        // Semua Biaya Personil
        // $subtotals = $manfeeDoc->detailPayments->groupBy('expense_type')->map(function ($items) {
        //     return $items->sum('nilai_biaya');
        // });

        $allBankAccounts = BankAccount::all();

        // Kecuali Biaya Non Personil
        $subtotals = $manfeeDoc->detailPayments->where('expense_type', '!=', 'Biaya Non Personil')
            ->groupBy('expense_type')
            ->map(function ($items) {
                return $items->sum('nilai_biaya');
            });


        $subtotalBiayaNonPersonil = $manfeeDoc->detailPayments
            ->whereIn('expense_type', ['Biaya Non Personil', 'biaya_non_personil'])
            ->sum('nilai_biaya');


        $latestApprover = DocumentApproval::where('document_id', $id)
            ->with('approver')
            ->latest('updated_at') // Ambil hanya yang paling baru
            ->first();

        $jenis_biaya = ['Biaya Personil', 'Biaya Non Personil', 'Biaya Lembur', 'THR', 'Kompesasi', 'SPPD', 'Add Cost'];

        // 🚀 **Gunakan Accurate Service untuk mendapatkan URL file**
        $apiResponseAkumulasi = $this->accurateOption->getInventoryList();
        $account_akumulasi = json_decode($apiResponseAkumulasi, true)['d'];

        $apiResponseDetail = $this->accurateOption->getAccountNonFeeList();
        $account_detailbiaya = json_decode($apiResponseDetail, true)['d'];

        // 🚀 **Gunakan DropboxController untuk mendapatkan URL file**
        $dropboxController = new DropboxController();

        $dropboxFolderName = '/attachments/';
        foreach ($manfeeDoc->attachments as $attachment) {
            $attachment->path = $dropboxController->getAttachmentUrl($attachment->path, $dropboxFolderName);
        }

        $dropboxFolderName = '/taxes/';
        foreach ($manfeeDoc->taxFiles as $taxFile) {
            $taxFile->path = $dropboxController->getAttachmentUrl($taxFile->path, $dropboxFolderName);
        }

        if ($manfeeDoc->status == '103') {
            $dropboxFolderName = '/rejected/';
            $manfeeDoc->path_rejected = $dropboxController->getAttachmentUrl($manfeeDoc->path_rejected, $dropboxFolderName);
        }


        return view('pages.ar-menu.management-fee.invoice-detail.show', compact('manfeeDoc', 'jenis_biaya', 'latestApprover', 'subtotals', 'subtotalBiayaNonPersonil', 'account_detailbiaya', 'account_akumulasi', 'allBankAccounts'));
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

        // $subtotals = $manfeeDoc->detailPayments->groupBy('expense_type')->map(function ($items) {
        //     return $items->sum('nilai_biaya');
        // });
        $allBankAccounts = BankAccount::all();

        $subtotals = $manfeeDoc->detailPayments->where('expense_type', '!=', 'Biaya Non Personil')
            ->groupBy('expense_type')
            ->map(function ($items) {
                return $items->sum('nilai_biaya');
            });

        $subtotalBiayaNonPersonil = $manfeeDoc->detailPayments
            ->whereIn('expense_type', ['Biaya Non Personil', 'biaya_non_personil'])
            ->sum('nilai_biaya');


        $rate_manfee = ['9', '10', '11', '12', '13'];
        $jenis_biaya = ['Biaya Personil', 'Biaya Non Personil', 'Biaya Lembur', 'THR', 'Kompesasi', 'SPPD', 'Add Cost'];

        // 🚀 **Gunakan Accurate Service untuk mendapatkan URL file**
        $apiResponseAkumulasi = $this->accurateOption->getInventoryList();
        $account_akumulasi = json_decode($apiResponseAkumulasi, true)['d'];

        $apiResponseDetail = $this->accurateOption->getAccountNonFeeList();
        $account_detailbiaya = json_decode($apiResponseDetail, true)['d'];

        // 🚀 **Gunakan DropboxController untuk mendapatkan URL file**
        $dropboxController = new DropboxController();

        $dropboxFolderName = '/attachments/';
        foreach ($manfeeDoc->attachments as $attachment) {
            $attachment->path = $dropboxController->getAttachmentUrl($attachment->path, $dropboxFolderName);
        }

        $dropboxFolderName = '/taxes/';
        foreach ($manfeeDoc->taxFiles as $taxFile) {
            $taxFile->path = $dropboxController->getAttachmentUrl($taxFile->path, $dropboxFolderName);
        }

        return view('pages.ar-menu.management-fee.invoice-detail.edit', compact('manfeeDoc', 'jenis_biaya', 'account_akumulasi', 'subtotals', 'subtotalBiayaNonPersonil', 'rate_manfee', 'account_detailbiaya', 'allBankAccounts'));
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

    /**
     * Proses Document with Approval Level
     */
    // public function processApproval(Request $request, $id)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $document = ManfeeDocument::findOrFail($id);
    //         $user = Auth::user();
    //         $userRole = $user->role;
    //         $department = $user->department;
    //         $previousStatus = $document->status;
    //         $currentRole = optional($document->latestApproval)->approver_role ?? 'maker';
    //         $message = $request->input('messages');

    //         // 🔹 1️⃣ Validasi: Apakah dokumen sudah di tahap akhir approval?
    //         if ($document->last_reviewers === 'pajak') {
    //             return back()->with('info', "Dokumen ini sudah berada di tahap akhir approval.");
    //         }

    //         // 🔹 2️⃣ Cek apakah dokumen dalam status revisi
    //         $isRevised = $document->status === '102';

    //         // 🔹 3️⃣  Jika revisi, lewati validasi karena `userRole` dan `currentRole` pasti berbeda
    //         if (!$isRevised && (!$userRole || $userRole !== $currentRole)) {
    //             return back()->with('error', "Anda tidak memiliki izin untuk menyetujui dokumen ini.");
    //         }

    //         $nextRole = null;
    //         $nextApprovers = collect();
    //         if ($isRevised) {
    //             // 🔹 4️⃣ Ambil APPROVER TERAKHIR secara keseluruhan
    //             $lastApprover = DocumentApproval::where('document_id', $document->id)
    //                 ->where('document_type', ManfeeDocument::class)
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
    //                 'document_type'  => ManfeeDocument::class,
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
    //         ManfeeDocHistories::create([
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
    //             'notifiable_type' => ManfeeDocument::class,
    //             'notifiable_id'   => $document->id,
    //             'messages'        => $message
    //                 ? "{$message}. Lihat detail: " . route('management-fee.show', $document->id)
    //                 : "Dokumen diproses oleh {$user->name}.",
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
            $document = ManfeeDocument::with(['attachments', 'accumulatedCosts'])->findOrFail($id);

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

            $document = ManfeeDocument::findOrFail($id);
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
                    ->where('document_type', ManfeeDocument::class)
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
                    'document_type'  => ManfeeDocument::class,
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
            ManfeeDocHistories::create([
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
                'notifiable_type' => ManfeeDocument::class,
                'notifiable_id'   => $document->id,
                'messages'        => $message
                    ? "{$message}. Lihat detail: " . route('management-fee.show', $document->id)
                    : "Dokumen telah disetujui oleh {$user->name}. Lihat detail: " . route('management-fee.show', $document->id),
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
            $document = ManfeeDocument::findOrFail($id);
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
                ->where('document_type', ManfeeDocument::class)
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
                    'document_type' => ManfeeDocument::class,
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

            // 🔹 5️⃣ Simpan riwayat revisi di `ManfeeDocHistories`
            ManfeeDocHistories::create([
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
                    'notifiable_type' => ManfeeDocument::class,
                    'notifiable_id'   => $document->id,
                    'messages'        =>  $message
                        ? "{$message}. Lihat detail: " . route('management-fee.show', $document->id)
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

    private function convertToRoman($month)
    {
        $romans = ["I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"];
        return $romans[$month - 1];
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

    public function rejected(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $document = ManfeeDocument::findOrFail($id);
        $user = auth()->user(); // Ambil user yang sedang login
        $userRole = $user->role;
        $previousStatus = $document->status;

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

        // Update dokumen
        $document->update([
            'reason_rejected' => $request->reason,
            'path_rejected'   => $dropboxPath,
            'status'          => 103, // Status dibatalkan
        ]);

        // Simpan ke riwayat
        ManfeeDocHistories::create([
            'document_id'     => $document->id,
            'performed_by'    => $user->id,
            'role'            => $userRole,
            'previous_status' => $previousStatus,
            'new_status'      => '103',
            'action'          => 'Rejected',
            'notes'           => "Dokumen dibatalkan oleh {$user->name} dengan alasan: {$request->reason}",
        ]);

        return redirect()->route('management-fee.show', $document->id)
            ->with('success', 'Dokumen berhasil dibatalkan.');
    }

    public function updateBankAccount(Request $request, $id)
    {
        $document = ManfeeDocument::findOrFail($id);
        $request->validate([
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
        ]);

        $document->update([
            'bank_account_id' => $request->bank_account_id
        ]);

        return response()->json(['success' => true]);
    }
}
