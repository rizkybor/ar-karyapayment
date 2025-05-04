<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;

use App\Models\User;
use App\Models\Contracts;
use App\Models\BankAccount;
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

use App\Services\PrivyService;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PrivyController;

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
        $contracts = Contracts::where('type', 'non_management_fee')->get();

        // Mengambil semua nomor dokumen dan info tambahan
        $numbers = $this->generateDocumentNumbers('NF');

        return view('pages/ar-menu/non-management-fee/create', array_merge([
            'contracts' => $contracts,
        ], $numbers));
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
        ]);

        // $numbers = $this->generateDocumentNumbers();

        $input = $request->only([
            'contract_id',
            'period',
            'letter_subject',
            'manfee_bill',
            'letter_number',
            'invoice_number',
            'receipt_number',
            'bank_account_id',
            'reference_document',
            'reason_rejected',
            'path_rejected',
            'last_reviewers',
        ]);

        $input['category'] = 'management_non_fee';
        $input['status'] = $request->status ?? 0;
        $input['status_print'] = false;
        $input['is_active'] = true;
        $input['created_by'] = auth()->id();
        $input['expired_at'] = Carbon::now()
            ->addMonthNoOverflow()
            ->day(15)
            ->setTime(0, 1, 0);

        try {
            // Simpan dokumen baru
            $nonManfeeDoc = NonManfeeDocument::create($input);
            return redirect()->route('non-management-fee.edit', $nonManfeeDoc)
                ->with('success', 'Data berhasil disimpan!');
        } catch (Exception $e) {
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

        $allBankAccounts = BankAccount::all();

        // Kecuali Biaya Non Personil
        $subtotals = $nonManfeeDocument->detailPayments->where('expense_type', '!=', 'Biaya Non Personil')
            ->groupBy('expense_type')
            ->map(function ($items) {
                return $items->sum('nilai_biaya');
            });


        $subtotalBiayaNonPersonil = $nonManfeeDocument->detailPayments
            ->whereIn('expense_type', ['Biaya Non Personil', 'biaya_non_personil'])
            ->sum('nilai_biaya');

        $latestApprover = DocumentApproval::where('document_id', $id)
            ->with('approver')
            ->latest('updated_at')
            ->first();

        # untuk value dropdown dalam detail biaya
        $jenis_biaya_default = ['Biaya Personil', 'Biaya Non Personil', 'Biaya Lembur', 'THR', 'Kompesasi', 'SPPD', 'Add Cost'];

        // Ambil semua expense_type unik dari detailPayments
        $existing_expense_types = $nonManfeeDocument->detailPayments
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

        // // uji coba
        // $apiResponseTest = $this->accurateOption->testAccount();
        // $array = json_decode($apiResponseTest, true);


        // // Daftar nama yang ingin diflagging
        // $searchNames = ['Reimburse Gaji TAD', 'PPh 23'];

        // // Tambahkan flagging Y/N ke setiap item
        // $dataWithFlag = array_filter(array_map(function ($item) use ($searchNames) {
        //     if (in_array($item['name'], $searchNames)) {
        //         $item['flag'] = 'Y';
        //         return $item;
        //     }
        //     return null; // buang yang tidak cocok
        // }, $array['d']));

        // // Reset ulang index array (optional, supaya rapi)
        // $dataWithFlag = array_values($dataWithFlag);

        // // Dump array
        // dd($array, $dataWithFlag);


        // 🚀 **Gunakan Accurate Service untuk mendapatkan URL file**
        $apiResponseAkumulasi = $this->accurateOption->getInventoryList();
        $account_akumulasi = json_decode($apiResponseAkumulasi, true)['d'];

        $apiResponseDetail = $this->accurateOption->getAccountNonFeeList();
        $account_detailbiaya = json_decode($apiResponseDetail, true)['d'];

        $apiResponsePayment = $this->accurateOption->getDataPenjualan($nonManfeeDocument->invoice_number);
        $payment_status_json = json_decode($apiResponsePayment, true)['d'];

        $payment_status = $payment_status_json[0]['statusName'] ?? null;

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

        if ($nonManfeeDocument->status == '103') {
            $dropboxFolderName = '/rejected/';
            $nonManfeeDocument->path_rejected = $dropboxController->getAttachmentUrl($nonManfeeDocument->path_rejected, $dropboxFolderName);
        }

        // 🚀 **Gunakan Accurate Service untuk mendapatkan URL file**
        $apiResponse = $this->accurateOption->getInventoryList();
        $optionAccount = json_decode($apiResponse, true)['d'];

        return view('pages/ar-menu/non-management-fee/invoice-detail/show', compact(
            'nonManfeeDocument',
            'latestApprover',
            'optionAccount',
            'jenis_biaya',
            'account_detailbiaya',
            'account_akumulasi',
            'subtotals',
            'subtotalBiayaNonPersonil',
            'allBankAccounts',
            'payment_status'
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
            'detailPayments',
            'attachments',
            'descriptions',
            'taxFiles'
        ])->findOrFail($id);

        $allBankAccounts = BankAccount::all();

        $subtotals = $nonManfeeDocument->detailPayments->where('expense_type', '!=', 'Biaya Non Personil')
            ->groupBy('expense_type')
            ->map(function ($items) {
                return $items->sum('nilai_biaya');
            });

        $subtotalBiayaNonPersonil = $nonManfeeDocument->detailPayments
            ->whereIn('expense_type', ['Biaya Non Personil', 'biaya_non_personil'])
            ->sum('nilai_biaya');

        # untuk value dropdown dalam detail biaya
        $jenis_biaya_default = ['Biaya Personil', 'Biaya Non Personil', 'Biaya Lembur', 'THR', 'Kompesasi', 'SPPD', 'Add Cost'];

        // Ambil semua expense_type unik dari detailPayments
        $existing_expense_types = $nonManfeeDocument->detailPayments
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
        $account_detailbiaya = json_decode($apiResponseAkumulasi, true)['d'];

        // $apiResponseDetail = $this->accurateOption->getAccountNonFeeList();
        // $account_detailbiaya = json_decode($apiResponseDetail, true)['d'];

        $apiResponsePayment = $this->accurateOption->getDataPenjualan($nonManfeeDocument->invoice_number);
        $payment_status_json = json_decode($apiResponsePayment, true)['d'];

        $payment_status = $payment_status_json[0]['statusName'] ?? null;

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
            'optionAccount',
            'jenis_biaya',
            'account_akumulasi',
            'account_detailbiaya',
            'subtotals',
            'subtotalBiayaNonPersonil',
            'allBankAccounts',
            'payment_status'
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
    //             // 🔹 3️⃣ Jika reviewer terakhir adalah 'pajak', kirim kembali ke 'perbendaharaan'
    //             $nextRole = 'perbendaharaan';
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
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         Log::error("Error saat approval dokumen [ID: {$id}]: " . $e->getMessage());
    //         return back()->with('error', "Terjadi kesalahan saat memproses approval.");
    //     }
    // }

    private function sendToPrivy(string $base64, string $typeSign, string $posX, string $posY): object
    {
        $request = new Request([
            'base64_pdf' => $base64,
            'type_sign' => $typeSign,
            "posX" => $posX,
            "posY" => $posY
        ]);

        $privyController = app()->make(PrivyController::class);
        $privyService = app()->make(PrivyService::class);

        return $privyController->generateDocument($request, $privyService,);
    }

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

            $document = NonManfeeDocument::with([
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
                    // ✅ Proccess AccurateService
                    $dataAccurate = [
                        'data' => $document,
                        'contract' => $document->contract,
                        'accumulatedCosts' => $document->accumulatedCosts,
                        'creator' => $document->creator,
                        'bankAccount' => $document->bankAccount,
                        'detailPayments' => $document->detailPayments,
                        'taxFiles' => $document->taxFiles,
                    ];

                    // LOGIC 1 - MENCARI DATA PELANGGAN ATAU BUAT BARU DATA PELANGAN DARI ACCURATE
                    $customersCheck = $this->accurateService->getAllCustomers([
                        'filter.keywords.op' => 'EQUAL',
                        'filter.keywords.val[0]' => $dataAccurate['contract']->employee_name
                    ]);

                    if (empty($customersCheck)) {
                        // ❌ Jika customer TIDAK ditemukan
                        $this->accurateService->saveCustomer($dataAccurate['contract']->employee_name);

                        $getCustomers = $this->accurateService->getAllCustomers([
                            'filter.keywords.op' => 'EQUAL',
                            'filter.keywords.val[0]' => $dataAccurate['contract']->employee_name
                        ]);
                        $customer = $getCustomers[0];
                    } else {
                        // ✅ Jika customer ditemukan
                        $customer = $customersCheck[0];
                    }

                    $customerDetailResponse = $this->accurateService->getCustomerDetail([
                        'customerNo' => $customer['customerNo']
                    ]);

                    $dataAccurate['customer'] = $customerDetailResponse['d'];

                    $itemsDetail = [];
                    foreach ($dataAccurate['detailPayments'] as $detailPayment) {
                        $accountId = $detailPayment->accountId ?? null;

                        if ($accountId) {
                            try {
                                $itemDetail = $this->accurateService->getItemDetail([
                                    'id' => $accountId
                                ]);
                                $itemsDetail[] = $itemDetail['d'];
                            } catch (Exception $e) {
                                // log error jika perlu
                                Log::error("Gagal mengambil detail item untuk accountId {$accountId}: " . $e->getMessage());
                            }
                        }
                    }

                    $dataAccurate['itemsAccurate'] = $itemsDetail;

                    // LOGIC 2 - INPUT SELURUH DATA PELANGAN KE ACCURATE
                    $apiResponsePostAccurate = $this->accurateService->postDataInvoice($dataAccurate);

                    dd($apiResponsePostAccurate, 'after hit accurate');


                    // ✅ Proccess Privy Service
                    // get base 64 from pdf template
                    $pdfController = app()->make(PDFController::class);
                    $base64letter = $pdfController->nonManfeeLetterBase64($document->id);
                    $base64inv = $pdfController->nonManfeeInvoiceBase64($document->id);
                    $base64kw = $pdfController->nonManfeeKwitansiBase64($document->id);

                    // PRIVY SERVICES
                    $createLetter = $this->sendToPrivy($base64letter, '0', '28.29', '677.18');
                    $createInvoice = $this->sendToPrivy($base64inv, '0', '543.30', '623.80');
                    $createKwitansi = $this->sendToPrivy($base64kw, '1', '510.78', '572.67');

                    $letterPrivy = $createLetter->getData();
                    $invoicePrivy = $createInvoice->getData();
                    $kwitansiPrivy = $createKwitansi->getData();

                    dd($letterPrivy, $invoicePrivy, $kwitansiPrivy, '<<< cek response PRIVY');
                } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
        NonManfeeDocHistory::create([
            'document_id'     => $document->id,
            'performed_by'    => $user->id,
            'role'            => $userRole,
            'previous_status' => $previousStatus,
            'new_status'      => '103',
            'action'          => 'Rejected',
            'notes'           => "Dokumen dibatalkan oleh {$user->name} dengan alasan: {$request->reason}",
        ]);

        return redirect()->route('non-management-fee.show', $document->id)
            ->with('success', 'Dokumen berhasil dibatalkan.');
    }

    private function generateDocumentNumbers(string $prefix = 'NF'): array
    {
        $monthRoman = $this->convertToRoman(date('n'));
        $year = date('Y');

        $lastNumber = NonManfeeDocument::orderByRaw('CAST(SUBSTRING(letter_number, 1, 6) AS UNSIGNED) DESC')
            ->value('letter_number');

        if (!$lastNumber) {
            $lastNumeric = 100;
        } else {
            preg_match('/^(\d{6})/', $lastNumber, $matches);
            $lastNumeric = intval($matches[1] ?? 100);

            if ($lastNumeric % 10 !== 0) {
                $lastNumeric = ceil($lastNumeric / 10) * 10;
            }
        }

        $nextNumber = $lastNumeric + 10;
        $baseNumber = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return [
            'letter_number'  => sprintf("%s/%s/KEU/KPU/Auto/%s/%s", $baseNumber, $prefix, $monthRoman, $year),
            'invoice_number' => sprintf("%s/%s/INV/KPU/Auto/%s/%s", $baseNumber, $prefix, $monthRoman, $year),
            'receipt_number' => sprintf("%s/%s/KW/KPU/Auto/%s/%s", $baseNumber, $prefix, $monthRoman, $year),
            'base_number'    => $baseNumber,
            'month_roman'    => $monthRoman,
            'year'           => $year,
        ];
    }

    public function updateBankAccount(Request $request, $id)
    {
        $document = NonManfeeDocument::findOrFail($id);
        $request->validate([
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
        ]);

        $document->update([
            'bank_account_id' => $request->bank_account_id
        ]);

        return response()->json(['success' => true]);
    }

    public function perihalUpdate(Request $request, $id)
    {
        $request->validate([
            'letter_subject' => 'required|string|max:255',
        ]);

        $doc = NonManfeeDocument::findOrFail($id);
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

        $doc = NonManfeeDocument::findOrFail($id);
        $doc->update([
            'reference_document' => $request->reference_document,
        ]);

        return redirect()->back()->with('success', 'Referensi dokumen berhasil diperbarui.');
    }
}
