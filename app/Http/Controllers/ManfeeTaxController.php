<?php

namespace App\Http\Controllers;

use App\Models\ManfeeDocTax;
use Illuminate\Http\Request;

class ManfeeTaxController extends Controller
{
    /**
     * Menampilkan detail tax berdasarkan ID dan document_id.
     */
    public function show($document_id, $tax_id)
    {
        $tax = ManfeeDocTax::where('document_id', $document_id)
            ->where('id', $tax_id)
            ->firstOrFail();

        return response()->json($tax);
    }

    /**
     * Menyimpan tax baru ke database.
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
        ManfeeDocTax::create([
            'document_id' => $id,
            'file_name' => $request->file_name,
            'path' => $path,
        ]);

        return redirect()->route('management-fee.edit', ['id' => $id])->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Mengupdate tax di database.
     */
    public function update(Request $request, $document_id, $tax_id)
    {
        $request->validate([
            'file_name' => 'required|string|max:255',
        ]);

        $tax = ManfeeDocTax::where('document_id', $document_id)
            ->where('id', $tax_id)
            ->firstOrFail();

        $tax->update([
            'file_name' => $request->file_name,
        ]);

        return response()->json(['message' => 'tax berhasil diperbarui.', 'data' => $tax]);
    }

    /**
     * Menghapus tax dari database berdasarkan ID dan document_id.
     */
    public function destroy($document_id, $tax_id)
    {
        $tax = ManfeeDocTax::where('document_id', $document_id)
            ->where('id', $tax_id)
            ->firstOrFail();

        $tax->delete();

        return redirect()->route('management-fee.edit', ['id' => $document_id])->with('success', 'tax berhasil dihapus!');
    }
}
