<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NonManfeeDocTax;

class NonManfeeTaxController extends Controller
{
    /**
     * Menampilkan detail pajak berdasarkan ID dan document_id.
     */
    public function show($document_id, $tax_id)
    {
        $tax = NonManfeeDocTax::where('document_id', $document_id)
                            ->where('id', $tax_id)
                            ->firstOrFail();

        return response()->json($tax);
    }

    /**
     * Menyimpan pajak baru ke database.
     */
    public function store(Request $request, $document_id)
    {
        $request->validate([
            'tax_type' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
            'amount' => 'required|numeric|min:0',
        ]);

        $tax = NonManfeeDocTax::create([
            'document_id' => $document_id,
            'tax_type' => $request->tax_type,
            'percentage' => $request->percentage,
            'amount' => $request->amount,
        ]);

        return response()->json(['message' => 'Pajak berhasil ditambahkan.', 'data' => $tax]);
    }

    /**
     * Mengupdate pajak di database.
     */
    public function update(Request $request, $document_id, $tax_id)
    {
        $request->validate([
            'tax_type' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
            'amount' => 'required|numeric|min:0',
        ]);

        $tax = NonManfeeDocTax::where('document_id', $document_id)
                            ->where('id', $tax_id)
                            ->firstOrFail();

        $tax->update([
            'tax_type' => $request->tax_type,
            'percentage' => $request->percentage,
            'amount' => $request->amount,
        ]);

        return response()->json(['message' => 'Pajak berhasil diperbarui.', 'data' => $tax]);
    }

    /**
     * Menghapus pajak dari database berdasarkan ID dan document_id.
     */
    public function destroy($document_id, $tax_id)
    {
        $tax = NonManfeeDocTax::where('document_id', $document_id)
                            ->where('id', $tax_id)
                            ->firstOrFail();

        $tax->delete();

        return response()->json(['message' => 'Pajak berhasil dihapus.']);
    }
}