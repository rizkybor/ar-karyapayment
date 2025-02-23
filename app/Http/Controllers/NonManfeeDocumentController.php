<?php

namespace App\Http\Controllers;

use App\Models\Contracts;
use App\Models\NonManfeeDocument;
use App\Models\MasterBillType;
use Illuminate\Http\Request;

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
        // Ambil kontrak yang memiliki type 'management_non_fee' dan belum memiliki dokumen
        $contracts = Contracts::where('type', 'management_non_fee')
            ->whereDoesntHave('nonManfeeDocuments')
            ->with('billTypes')
            ->get();

        // dd($contracts);

        // Format Romawi untuk bulan
        $monthRoman = $this->convertToRoman(date('n'));
        $year = date('Y');

        // Nomer terakhir + 10
        $lastNumber = NonManfeeDocument::max('letter_number');
        $nextNumber = $lastNumber ? (intval(substr($lastNumber, 4, 6)) + 10) : 100;

        // Format nomor surat, invoice, dan kwitansi
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
        // dd("Request diterima:", $request->all());

        // $request->validate([
        //     'contract_id' => 'required|exists:contracts,id',
        //     'period' => 'required',
        //     'letter_subject' => 'required',
        //     'bill_type' => 'required|exists:mst_bill_type,bill_type',
        // ]);

        // // Cek apakah contract_id sudah memiliki dokumen management_fee
        // $existingDocument = NonManfeeDocument::where('contract_id', $request->contract_id)->first();
        // if ($existingDocument) {
        //     return redirect()->back()->withErrors(['contract_id' => 'Dokumen untuk kontrak ini sudah ada.']);
        // }


        // $monthRoman = $this->convertToRoman(date('n'));
        // $year = date('Y');

        // $lastNumber = NonManfeeDocument::max('letter_number');
        // $nextNumber = $lastNumber ? (intval(substr($lastNumber, 4, 6)) + 10) : 100;

        // $letterNumber = sprintf("No. %06d/KEU/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        // $invoiceNumber = sprintf("No. %06d/KW/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        // $receiptNumber = sprintf("No. %06d/INV/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);


        // $input = $request->all();
        // $input['letter_number'] = $letterNumber;
        // $input['invoice_number'] = $invoiceNumber;
        // $input['receipt_number'] = $receiptNumber;
        // $input['category'] = 'management_fee';
        // $input['status'] = $input['status'] ?? 0;
        // $input['created_by'] = auth()->id();

        $documentId = 1; // ID Dummy

        // Redirect ke halaman detail setelah "penyimpanan" berhasil
        return redirect()->route('management-non-fee.show', ['id' => $documentId])
            ->with('success', 'Data berhasil disimpan!');

        // try {
        //     NonManfeeDocument::create($input);

        //     return redirect()->route('management-fee.index')->with('success', 'Data berhasil disimpan!');
        // } catch (\Exception $e) {
        //     return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // $document = NonManfeeDocument::findOrFail($id);

        $document = [
            'id' => $id,
            'letter_number' => 'No. 001/KEU/KPU/SOL/I/2025',
            'invoice_number' => 'No. 001/KW/KPU/SOL/I/2025',
            'receipt_number' => 'No. 001/INV/KPU/SOL/I/2025',
            'contract_id' => 123,
            'period' => 'Januari 2025',
            'letter_subject' => 'Tagihan Jasa Konsultasi',
            'bill_type' => 'Non-Manfee',
            'status' => 'Pending',
            'created_by' => 'Admin',
            'created_at' => now()->format('d M Y H:i'),
        ];

        // Data Dummy Lampiran
        $attachments = [
            (object) ['id' => 1, 'name' => 'BAP'],
            (object) ['id' => 2, 'name' => 'Invoice'],
            (object) ['id' => 3, 'name' => 'Kontrak Kerja'],
        ];

        return view('pages/ar-menu/management-non-fee/invoice-detail/show', compact('document', 'attachments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // $document = NonManfeeDocument::findOrFail($id);

        $document = [
            'id' => $id,
            'letter_number' => 'No. 001/KEU/KPU/SOL/I/2025',
            'invoice_number' => 'No. 001/KW/KPU/SOL/I/2025',
            'receipt_number' => 'No. 001/INV/KPU/SOL/I/2025',
            'contract_id' => 123,
            'period' => 'Januari 2025',
            'letter_subject' => 'Tagihan Jasa Konsultasi',
            'bill_type' => 'Non-Manfee',
            'status' => 'Pending',
            'created_by' => 'Admin',
            'created_at' => now()->format('d M Y H:i'),
        ];

         // Data Dummy Lampiran
         $attachments = [
            (object) ['id' => 1, 'name' => 'BAP'],
            (object) ['id' => 2, 'name' => 'Invoice'],
            (object) ['id' => 3, 'name' => 'Kontrak Kerja'],
        ];

        // Data Dummy Lampiran
        $files_faktur = [
            (object) ['id' => 1, 'name' => 'File Faktur Pajak'],
        ];

        return view('pages/ar-menu/management-non-fee/invoice-detail/edit', compact('document', 'attachments', 'files_faktur'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NonManfeeDocument $NonManfeeDocument)
    {
        //
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
        // Simulasi pengambilan data lampiran terkait dokumen tertentu
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


    private function convertToRoman($month)
    {
        $romans = ["I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"];
        return $romans[$month - 1];
    }
}
