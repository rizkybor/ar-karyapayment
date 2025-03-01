<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NonManfeeDocTax;

class NonManfeeTaxController extends Controller
{
    /**
     * Menampilkan detail pajak berdasarkan ID dan id.
     */
    public function show($id, $tax_id)
    {
        $tax = NonManfeeDocTax::where('id', $id)
                            ->where('id', $tax_id)
                            ->firstOrFail();

        return response()->json($tax);
    }

    /**
     * Menyimpan pajak baru ke database.
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'file_name' => 'required|string|max:255',
            'file' => 'required|file|max:2048',
        ]);

        // Simpan file dan ambil path-nya
        $path = $request->file('file')->store('attachments', 'public');

        // Simpan ke database
        NonManfeeDocTax::create([
            'document_id' => $id,
            'file_name' => $request->file_name,
            'path' => $path,
        ]);

        return redirect()->route('non-management-fee.edit', ['id' => $id])->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Mengupdate pajak di database.
     */
    public function update(Request $request, $id, $tax_id)
    {
        $request->validate([
            'tax_type' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
            'amount' => 'required|numeric|min:0',
        ]);

        $tax = NonManfeeDocTax::where('id', $id)
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
     * Menghapus pajak dari database berdasarkan ID dan id.
     */
    public function destroy($id, $tax_id)
    {
        $tax = NonManfeeDocTax::where('document_id', $id)
                            ->where('id', $tax_id)
                            ->firstOrFail();

        $tax->delete();

        return redirect()->route('non-management-fee.edit', ['id' => $id])->with('success', 'Tax berhasil dihapus!');
    }
}