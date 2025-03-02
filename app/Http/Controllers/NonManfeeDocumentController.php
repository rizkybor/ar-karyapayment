<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Contracts;
// use Yajra\DataTables\DataTables;
use Yajra\DataTables\Facades\DataTables;
use App\Models\NonManfeeDocument;
use App\Models\NonManfeeDocAccumulatedCost;
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
            'taxFiles'
        ])->findOrFail($id);

        return view('pages/ar-menu/non-management-fee/invoice-detail/show', compact(
            'nonManfeeDocument'
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
    public function processApproval($id)
    {
        DB::beginTransaction(); // Mulai transaksi database
        try {
            $document = NonManfeeDocument::findOrFail($id);
            $currentRole = optional($document->latestApproval)->role ?? 'maker';
            $department = Auth::user()->department; // Ambil departemen user yang mengajukan dokumen

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
            $alreadyApproved = DocumentApproval::where('document_id', $document->id)
                ->where('document_type', NonManfeeDocument::class)
                ->where('approver_id', Auth::id())
                ->exists();

            if ($alreadyApproved) {
                return back()->with('error', "Anda sudah menyetujui dokumen ini sebelumnya.");
            }

            // 🔹 4️⃣ Dapatkan role approval berikutnya berdasarkan flowchart
            $nextRole = $this->getNextApprovalRole($currentRole, $department);

            if (!$nextRole) {
                return back()->with('info', "Dokumen ini sudah berada di tahap akhir approval.");
            }

            // 🔹 5️⃣ Ambil user dengan role berikutnya
            $nextApprovers = User::where('role', $nextRole)
                ->when($nextRole === 'kadiv', function ($query) use ($department) {
                    return $query->whereRaw("LOWER(department) = ?", [strtolower($department)]);
                })
                ->get();

            if ($nextApprovers->isEmpty()) {
                return back()->with('error', "Tidak ada user dengan role {$nextRole}" .
                    ($nextRole === 'kadiv' ? " di departemen {$department}." : "."));
            }

            // 🔹 6️⃣ Simpan approval untuk user yang menerima dokumen berikutnya
            foreach ($nextApprovers as $nextApprover) {
                DocumentApproval::create([
                    'document_id'    => $document->id,
                    'document_type'  => NonManfeeDocument::class,
                    'approver_id'    => $nextApprover->id, // 🔥 Pastikan ini adalah user penerima, bukan user saat ini
                    'role'           => $nextRole, // 🔥 Role dari user penerima
                    'status'         => (string) array_search($nextRole, $this->approvalStatusMap()),
                    'approved_at'    => null, // 🔥 Set null karena approval belum dilakukan
                ]);
            }

            // 🔹 7️⃣ Perbarui reviewer terakhir di dokumen
            $document->update([
                'last_reviewers' => $nextRole,
                'status'         => (string) array_search($nextRole, $this->approvalStatusMap()),
            ]);

            // 🔹 8️⃣ Kirim Notifikasi ke Role Berikutnya
            $notification = Notification::create([
                'type'            => InvoiceApprovalNotification::class,
                'notifiable_type' => NonManfeeDocument::class,
                'notifiable_id'   => $document->id,
                'data'            => json_encode([
                    'id'            => $document->id,
                    'invoice_number' => $document->invoice_number,
                    'action'        => 'approved',
                    'message'       => "Invoice #{$document->invoice_number} membutuhkan persetujuan dari {$nextRole}.",
                    'url'           => route('non-management-fee.show', $document->id),
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
            Log::error("Error saat approval dokumen [ID: {$document->id}]: " . $e->getMessage());
            return back()->with('error', "Terjadi kesalahan saat memproses approval.");
        }
    }
    // public function processApproval($id)
    // {
    //     DB::beginTransaction(); // Mulai transaksi database
    //     try {
    //         $document = NonManfeeDocument::findOrFail($id);
    //         $currentRole = optional($document->latestApproval)->role ?? 'maker';
    //         $department = Auth::user()->department; // Ambil departemen user yang mengajukan dokumen

    //         // 🔹 1️⃣ Validasi: Apakah dokumen sudah di tahap akhir approval?
    //         if ($document->last_reviewers === 'pajak') {
    //             return back()->with('info', "Dokumen ini sudah berada di tahap akhir approval.");
    //         }

    //         // 🔹 2️⃣ Validasi: Apakah user memiliki role yang diizinkan?
    //         $userRole = Auth::user()->role;
    //         if (!$userRole || $userRole !== $currentRole) {
    //             return back()->with('error', "Anda tidak memiliki izin untuk menyetujui dokumen ini.");
    //         }

    //         // 🔹 3️⃣ Validasi: Apakah user sudah pernah approve dokumen ini sebelumnya?
    //         $alreadyApproved = DocumentApproval::where('document_id', $document->id)
    //             ->where('document_type', NonManfeeDocument::class)
    //             ->where('approver_id', Auth::id())
    //             ->exists();

    //         if ($alreadyApproved) {
    //             return back()->with('error', "Anda sudah menyetujui dokumen ini sebelumnya.");
    //         }

    //         // 🔹 4️⃣ Dapatkan role approval berikutnya berdasarkan flowchart
    //         $nextRole = $this->getNextApprovalRole($currentRole, $department);

    //         if (!$nextRole) {
    //             return back()->with('info', "Dokumen ini sudah berada di tahap akhir approval.");
    //         }

    //         // 🔹 5️⃣ Ambil user dengan role berikutnya
    //         $nextApprovers = User::where('role', $nextRole)
    //             ->when($nextRole === 'kadiv', function ($query) use ($department) {
    //                 return $query->whereRaw("LOWER(department) = ?", [strtolower($department)]);
    //             })
    //             ->get();

    //         if ($nextApprovers->isEmpty()) {
    //             return back()->with('error', "Tidak ada user dengan role {$nextRole}" .
    //                 ($nextRole === 'kadiv' ? " di departemen {$department}." : "."));
    //         }

    //         // 🔹 6️⃣ Simpan approval ke tabel `document_approvals`
    //         DocumentApproval::create([
    //             'document_id'    => $document->id,
    //             'document_type'  => NonManfeeDocument::class,
    //             'approver_id'    => Auth::id(),
    //             'role'           => $currentRole,
    //             'status'         => (string) array_search($currentRole, $this->approvalStatusMap()),
    //             'approved_at'    => now(),
    //         ]);

    //         // 🔹 7️⃣ Perbarui reviewer terakhir di dokumen
    //         $document->update([
    //             'last_reviewers' => $nextRole,
    //             'status'         => (string) array_search($nextRole, $this->approvalStatusMap()),
    //         ]);

    //         // 🔹 8️⃣ Kirim Notifikasi ke Role Berikutnya
    //         $notification = Notification::create([
    //             'type'            => InvoiceApprovalNotification::class,
    //             'notifiable_type' => NonManfeeDocument::class,
    //             'notifiable_id'   => $document->id,
    //             'data'            => json_encode([
    //                 'id'            => $document->id,
    //                 'invoice_number' => $document->invoice_number,
    //                 'action'        => 'approved',
    //                 'message'       => "Invoice #{$document->invoice_number} membutuhkan persetujuan dari {$nextRole}.",
    //                 'url'           => route('non-management-fee.show', $document->id),
    //             ]),
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);

    //         // 🔹 9️⃣ Kirim notifikasi ke setiap user dengan role berikutnya
    //         foreach ($nextApprovers as $user) {
    //             NotificationRecipient::create([
    //                 'notification_id' => $notification->id,
    //                 'user_id'         => $user->id,
    //                 'read_at'         => null,
    //                 'created_at'      => now(),
    //                 'updated_at'      => now(),
    //             ]);
    //         }

    //         DB::commit(); // Simpan semua perubahan dalam transaksi
    //         return back()->with('success', "Dokumen telah disetujui dan diteruskan ke {$nextRole}.");
    //     } catch (\Exception $e) {
    //         DB::rollBack(); // Jika ada error, batalkan semua perubahan
    //         Log::error("Error saat approval dokumen [ID: {$document->id}]: " . $e->getMessage());
    //         return back()->with('error', "Terjadi kesalahan saat memproses approval.");
    //     }
    // }

    /**
     * Fungsi untuk mendapatkan role berikutnya dalam flowchart.
     */
    private function getNextApprovalRole($currentRole, $department = null)
    {
        // Jika role saat ini adalah staff, maka approval selanjutnya ke Kadiv dalam departemen yang sama
        if ($currentRole === 'maker' && $department) {
            return 'kadiv'; // Approval ke Kadiv dalam departemen yang sama
        }

        // Setelah Kadiv, approval akan mengikuti flow umum
        $flow = [
            'kadiv' => 'bendahara',
            'bendahara' => 'manager_anggaran',
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
            '100' => 'completed',
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
