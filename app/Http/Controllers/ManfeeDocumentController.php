<?php

namespace App\Http\Controllers;

use App\Models\Contracts;
use App\Models\ManfeeDocument;
use Illuminate\Http\Request;

class ManfeeDocumentController extends Controller
{
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
        // $contracts = Contracts::all();
        $contracts = Contracts::where('type', 'management_fee')->get();

        // Ambil bulan dan tahun dalam format Romawi
        $monthRoman = $this->convertToRoman(date('n'));
        $year = date('Y');

        // Generate nomor terakhir + 10
        $lastDocument = ManfeeDocument::latest()->first();
        $nextNumber = $lastDocument ? intval(substr($lastDocument->letter_number, 4, 3)) + 10 : 100;

        // Format nomor surat
        $letterNumber = sprintf("No. %06d/KEU/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $invoiceNumber = sprintf("No. %06d/KW/KPU/SOL/%s/%s", $nextNumber / 10, $monthRoman, $year);
        $receiptNumber = sprintf("No. %06d/INV/KPU/SOL/%s/%s", $nextNumber / 10, $monthRoman, $year);

        return view('pages/ar-menu/management-fee/create', compact('contracts', 'letterNumber', 'invoiceNumber', 'receiptNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd("Request diterima:", $request->all());

        // Validasi tanpa 'category' dan 'status' karena akan di-set otomatis
        $request->validate([
            'contract_id' => 'required',
            'period' => 'required',
            'letter_subject' => 'required',
        ]);


        $monthRoman = $this->convertToRoman(date('n'));
        $year = date('Y');

        // Ambil nomor terakhir dari database
        $lastDocument = ManfeeDocument::latest()->first();
        $nextNumber = $lastDocument ? intval(substr($lastDocument->letter_number, 4, 3)) + 10 : 100;

        // Generate nomor dokumen
        $letterNumber = sprintf("No. %06d/KEU/KPU/SOL/%s/%s", $nextNumber, $monthRoman, $year);
        $invoiceNumber = sprintf("No. %06d/KW/KPU/SOL/%s/%s", $nextNumber / 10, $monthRoman, $year);
        $receiptNumber = sprintf("No. %06d/INV/KPU/SOL/%s/%s", $nextNumber / 10, $monthRoman, $year);


        $input = $request->all();
        $input['letter_number'] = $letterNumber;
        $input['invoice_number'] = $invoiceNumber;
        $input['receipt_number'] = $receiptNumber;
        $input['category'] = 'management_fee';
        $input['status'] = $input['status'] ?? 0;
        $input['created_by'] = auth()->id();

        try {
            ManfeeDocument::create($input);

            return redirect()->route('management-fee.index')->with('success', 'Data berhasil disimpan!');
        } catch (\Exception $e) {
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
    public function destroy(ManfeeDocument $manfeeDocument)
    {
        //
    }

    private function convertToRoman($month)
    {
        $romans = ["I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"];
        return $romans[$month - 1];
    }
}
