<?php

namespace App\Http\Controllers;

use App\Models\Contracts;
use App\Models\ManfeeDocument;
use App\Models\MasterBillType;
use Illuminate\Http\Request;

class ManfeeDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $manfeeDocs = ManfeeDocument::with('contract')->get();
        return view('pages/ar-menu/management-fee/index', compact('manfeeDocs'));
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

        // Nomer terakhir + 10
        $lastNumber = ManfeeDocument::max('letter_number');
        $nextNumber = $lastNumber ? (intval(substr($lastNumber, 4, 6)) + 10) : 100;

        // Format nomor surat, invoice, dan kwitansi
        $letterNumber = sprintf("No. %06d/KEU/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $invoiceNumber = sprintf("No. %06d/KW/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $receiptNumber = sprintf("No. %06d/INV/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);

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

        $lastNumber = ManfeeDocument::max('letter_number');
        $nextNumber = $lastNumber ? (intval(substr($lastNumber, 4, 6)) + 10) : 100;

        $letterNumber = sprintf("No. %06d/KEU/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $invoiceNumber = sprintf("No. %06d/KW/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $receiptNumber = sprintf("No. %06d/INV/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);


        $input = $request->only(['contract_id', 'period', 'letter_subject', 'manfee_bill']);
        $input['letter_number'] = $letterNumber;
        $input['invoice_number'] = $invoiceNumber;
        $input['receipt_number'] = $receiptNumber;
        $input['category'] = 'management_fee';
        $input['status'] = $request->status ?? 0;
        $input['created_by'] = auth()->id();

        try {
            ManfeeDocument::create($input);

            return redirect()->route('management-fee.index')->with('success', 'Data berhasil disimpan!');
        } catch (\Exception $e) {
            // dd($e);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ManfeeDocument $manfeeDocument)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ManfeeDocument $manfeeDocument)
    {
        //
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
}
