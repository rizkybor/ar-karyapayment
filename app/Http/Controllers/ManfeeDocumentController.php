<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;

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

use App\Services\PrivyService;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PrivyController;

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
        $baseNumber = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        // Generate default numbers with SOL
        $letterNumber = sprintf("%s/MF/KEU/KPU/Auto/%s/%s", $baseNumber, $monthRoman, $year);
        $invoiceNumber = sprintf("%s/MF/INV/KPU/Auto/%s/%s", $baseNumber, $monthRoman, $year);
        $receiptNumber = sprintf("%s/MF/KW/KPU/Auto/%s/%s", $baseNumber, $monthRoman, $year);

        return view('pages/ar-menu/management-fee/create', compact(
            'contracts',
            'letterNumber',
            'invoiceNumber',
            'receiptNumber',
            'baseNumber',
            'monthRoman',
            'year'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'period' => 'required',
            'letter_subject' => 'required',
            'manfee_bill' => 'required',
            'letter_number' => 'required',
            'invoice_number' => 'required',
            'receipt_number' => 'required',
        ]);

        // Ambil data kontrak untuk mendapatkan nama perusahaan
        $contract = Contracts::find($request->contract_id);
        $employeeName = $contract->employee_name;

        // Ekstrak inisial perusahaan
        $companyInitial = 'SOL';
        if (preg_match('/PT\.\s*([^\s,]+)/i', $employeeName, $matches)) {
            $companyInitial = $matches[1];
        } elseif (!empty($employeeName)) {
            $companyInitial = explode(' ', $employeeName)[0];
        }

        $monthRoman = $this->convertToRoman(date('n'));
        $year = date('Y');

        // Gunakan nomor yang sudah di-generate di form
        $input = $request->only([
            'contract_id',
            'period',
            'letter_subject',
            'manfee_bill',
            'letter_number',
            'invoice_number',
            'receipt_number',
            'reference_document'
        ]);

        $input['category'] = 'management_fee';
        $input['status'] = $request->status ?? 0;
        $input['is_active'] = true;
        $input['created_by'] = auth()->id();
        $input['expired_at'] = Carbon::now()->addDays(30)->setTime(0, 1, 0);

        try {
            $manfeeDoc = ManfeeDocument::create($input);
            return redirect()->route('management-fee.edit', $manfeeDoc)->with('success', 'Data berhasil disimpan!');
        } catch (\Exception $e) {
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

        # untuk value dropdown dalam detail biaya
        $jenis_biaya_default = ['Biaya Personil', 'Biaya Non Personil'];

        // Ambil semua expense_type unik dari detailPayments
        $existing_expense_types = $manfeeDoc->detailPayments
            ->pluck('expense_type')
            ->unique()
            ->filter()
            ->values();

        // Gabungkan default dan yang ada di DB jika belum termasuk
        $jenis_biaya = collect($jenis_biaya_default)
            ->merge($existing_expense_types)
            ->unique()
            ->values()
            ->all();

        // 🚀 **Gunakan Accurate Service untuk mendapatkan URL file**
        $apiResponseAkumulasi = $this->accurateOption->getInventoryList();
        $account_akumulasi = json_decode($apiResponseAkumulasi, true)['d'];

        $apiResponseDetail = $this->accurateOption->getAccountNonFeeList();
        $account_detailbiaya = json_decode($apiResponseDetail, true)['d'];

        $apiResponsePayment = $this->accurateOption->getDataPenjualan($manfeeDoc->invoice_number);
        $payment_status_json = json_decode($apiResponsePayment, true)['d'];

        $payment_status = $payment_status_json[0]['statusName'] ?? null;

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


        return view('pages.ar-menu.management-fee.invoice-detail.show', compact('manfeeDoc', 'jenis_biaya', 'latestApprover', 'subtotals', 'subtotalBiayaNonPersonil', 'account_detailbiaya', 'account_akumulasi', 'allBankAccounts', 'payment_status'));
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

        # untuk value dropdown dalam detail biaya
        $jenis_biaya_default = ['Biaya Personil', 'Biaya Non Personil'];

        // Ambil semua expense_type unik dari detailPayments
        $existing_expense_types = $manfeeDoc->detailPayments
            ->pluck('expense_type')
            ->unique()
            ->filter()
            ->values();

        // Gabungkan default dan yang ada di DB jika belum termasuk
        $jenis_biaya = collect($jenis_biaya_default)
            ->merge($existing_expense_types)
            ->unique()
            ->values()
            ->all();

        // 🚀 **Gunakan Accurate Service untuk mendapatkan URL file**
        $apiResponseAkumulasi = $this->accurateOption->getInventoryList();
        $account_akumulasi = json_decode($apiResponseAkumulasi, true)['d'];

        $apiResponseDetail = $this->accurateOption->getInventoryList();
        $account_detailbiaya = json_decode($apiResponseDetail, true)['d'];

        $apiResponsePayment = $this->accurateOption->getDataPenjualan($manfeeDoc->invoice_number);
        $payment_status_json = json_decode($apiResponsePayment, true)['d'];

        $payment_status = $payment_status_json[0]['statusName'] ?? null;

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

        return view('pages.ar-menu.management-fee.invoice-detail.edit', compact('manfeeDoc', 'jenis_biaya', 'account_akumulasi', 'subtotals', 'subtotalBiayaNonPersonil', 'rate_manfee', 'account_detailbiaya', 'allBankAccounts', 'payment_status'));
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

    public function perihalUpdate(Request $request, $id)
    {
        $request->validate([
            'letter_subject' => 'required|string|max:255',
        ]);

        $doc = ManfeeDocument::findOrFail($id);
        $doc->update([
            'letter_subject' => $request->letter_subject,
        ]);

        return redirect()->back()->with('success', 'Perihal berhasil diperbarui.');
    }

    public function referenceUpdate(Request $request, $id)
    {
        $request->validate([
            'reference_document' => 'nullable|string|max:255',
        ]);

        $doc = ManfeeDocument::findOrFail($id);
        $doc->update([
            'reference_document' => $request->reference_document,
        ]);

        return redirect()->back()->with('success', 'Referensi dokumen berhasil diperbarui.');
    }

    // Privy

    private function sendToPrivy(string $base64, string $typeSign, string $posX, string $posY, string $docType, string $noSurat): object
    {
        $request = new Request([
            'base64_pdf' => $base64,
            'type_sign' => $typeSign,
            "posX" => $posX,
            "posY" => $posY,
            'docType' => $docType,
            'noSurat' => $noSurat
        ]);

        $privyController = app()->make(PrivyController::class);
        $privyService = app()->make(PrivyService::class);

        dd([
            'base64_pdf' => $request->base64_pdf,
            'type_sign' => $request->type_sign,
            'posX' => $request->posX,
            'posY' => $request->posY,
            'docType' => $docType,
            'noSurat' => $noSurat
        ]);

        return $privyController->generateDocument($request, $privyService,);
    }


    public function processApproval(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // 🔍 Ambil dokumen berdasarkan ID
            $document = ManfeeDocument::with(['detailPayments', 'accumulatedCosts', 'attachments'])->findOrFail($id);

            // ✅ Cek apakah ada detail biaya (detailPayments)
            if ($document->detailPayments->isEmpty()) {
                return back()->with(
                    'error',
                    "Dokumen tidak dapat diproses karena belum memiliki detail biaya."
                );
            }

            // ✅ Cek apakah ada akumulasi biaya (accumulatedCosts)
            if ($document->accumulatedCosts->pluck('account')[0] == null) {
                return back()->with(
                    'error',
                    "Dokumen tidak dapat diproses karena tidak ada akun yang terpilih pada akumulasi biaya."
                );
            }

            // ✅ Cek apakah ada lampiran (attachments)
            if ($document->attachments->isEmpty()) {
                return back()->with(
                    'error',
                    "Dokumen tidak dapat diproses karena belum memiliki lampiran."
                );
            }

            $document = ManfeeDocument::with([
                'detailPayments',
                'taxFiles',
                'contract',
                'creator',
                'bankAccount',
                'accumulatedCosts'
            ])->findOrFail($id);
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

            // 🔹 3️⃣ Jika reviewer terakhir adalah 'pajak', kirim kembali ke 'perbendaharaan'
            if ($document->last_reviewers === 'pajak') {
                // ✅ Cek apakah ada faktur pajak (tax files)
                if ($document->taxFiles->isEmpty()) {
                    return back()->with(
                        'error',
                        "Faktur pajak belum ada, upload faktur pajak dahulu sebelum anda melakukan approval"
                    );
                }

                try {
                    // ✅ Kirim ke AccurateService
                    // $dataAccurate = [
                    //     'data' => $document,
                    //     'contract' => $document->contract,
                    //     'accumulatedCosts' => $document->accumulatedCosts,
                    //     'creator' => $document->creator,
                    //     'bankAccount' => $document->bankAccount,
                    //     'detailPayments' => $document->detailPayments,
                    //     'taxFiles' => $document->taxFiles,
                    // ];

                    // // LOGIC 1 - MENCARI DATA PELANGGAN ATAU BUAT BARU DATA PELANGAN DARI ACCURATE
                    // $customersCheck = $this->accurateService->getAllCustomers([
                    //     'filter.keywords.op' => 'EQUAL',
                    //     'filter.keywords.val[0]' => $dataAccurate['contract']->employee_name
                    // ]);

                    // if (empty($customersCheck)) {
                    //     // ❌ Jika customer TIDAK ditemukan
                    //     $this->accurateService->saveCustomer($dataAccurate['contract']->employee_name);

                    //     $getCustomers = $this->accurateService->getAllCustomers([
                    //         'filter.keywords.op' => 'EQUAL',
                    //         'filter.keywords.val[0]' => $dataAccurate['contract']->employee_name
                    //     ]);
                    //     $customer = $getCustomers[0];
                    // } else {
                    //     // ✅ Jika customer ditemukan
                    //     $customer = $customersCheck[0];
                    // }

                    // $customerDetailResponse = $this->accurateService->getCustomerDetail([
                    //     'customerNo' => $customer['customerNo']
                    // ]);

                    // $dataAccurate['customer'] = $customerDetailResponse['d'];

                    // $detailPayments = $dataAccurate['detailPayments'];

                    // foreach ($detailPayments as $index => $detailPayment) {
                    //     $accountId = $detailPayment->accountId ?? null;

                    //     if ($accountId) {
                    //         try {
                    //             $itemDetail = $this->accurateService->getItemDetail([
                    //                 'id' => $accountId,
                    //             ]);

                    //             // Masukkan hasil detail item ke dalam masing-masing objek detailPayment
                    //             $detailPayments[$index]['item_detail'] = $itemDetail['d'] ?? null;
                    //         } catch (Exception $e) {
                    //             Log::error("Gagal mengambil detail item untuk accountId {$accountId}: " . $e->getMessage());
                    //             $detailPayments[$index]['item_detail'] = null;
                    //         }
                    //     }
                    // }

                    // // Replace detailPayments di $dataAccurate dengan yang sudah di-update
                    // $dataAccurate['detailPayments'] = $detailPayments;

                    // // LOGIC 2 - INPUT SELURUH DATA PELANGAN KE ACCURATE
                    // $apiResponsePostAccurate = $this->accurateService->postDataInvoice($dataAccurate);

                    // dd($apiResponsePostAccurate, 'after hit accurate, check accurate');


                    // // ✅ Proccess Privy Service
                    // // get base 64 from pdf template
                    $pdfController = app()->make(PDFController::class);
                    $base64letter = $pdfController->manfeeLetterBase64($document->id);
                    $base64inv = $pdfController->manfeeInvoiceBase64($document->id);
                    $base64kw = $pdfController->manfeeKwitansiBase64($document->id);
                    
                    // CREATE REFERENCE NUMBER DOCUMENT
                    $tanggal = Carbon::now();
                    $noSurat = $document->letter_number;
                    $referenceNumber = 'REF' . $tanggal->format('Ymd') . $noSurat;
                    $referenceNumber = str_replace('/', '', $referenceNumber);
                    
                    // // PRIVY SERVICES
                    $createLetter = $this->sendToPrivy($base64letter, '0', '25.94', '690.84', $referenceNumber, $noSurat);
                    $createInvoice = $this->sendToPrivy($base64inv, '0', '524.66', '653.47', $referenceNumber, $noSurat);
                    $createKwitansi = $this->sendToPrivy($base64kw, '1', '506,54', '601,55', $referenceNumber, $noSurat);

                    // $letterPrivy = $createLetter->getData();
                    // $invoicePrivy = $createInvoice->getData();
                    // $kwitansiPrivy = $createKwitansi->getData();

                    // dd($letterPrivy, $invoicePrivy, $kwitansiPrivy, '<<< cek response PRIVY');
                } catch (\Exception $e) {
                    return back()->with('error', 'Gagal kirim data ke Accurate: ' . $e->getMessage());
                }
                // ✅ Lanjutkan proses approval
                $nextRole = 'perbendaharaan';
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
            'kadiv'               => 'perbendaharaan',
            'perbendaharaan'      => 'manager_anggaran',
            'manager_anggaran'    => 'direktur_keuangan',
            'direktur_keuangan'   => 'pajak',
            'pajak'               => 'perbendaharaan'
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
            '2'   => 'perbendaharaan',
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
