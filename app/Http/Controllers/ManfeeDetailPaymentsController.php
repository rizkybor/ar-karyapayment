<?php

namespace App\Http\Controllers;

use App\Models\ManfeeDocDetailPayments;
use Illuminate\Http\Request;

class ManfeeDetailPaymentsController extends Controller
{
    public function show($document_id, $docdetail_id)
    {
        $docdetail = ManfeeDocDetailPayments::where('document_id', $document_id)
            ->where('id', $docdetail_id)
            ->firstOrFail();

        return response()->json($docdetail);
    }

    /**
     * Menyimpan lampiran baru ke database.
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
        ManfeeDocDetailPayments::create([
            'document_id' => $id,
            'file_name' => $request->file_name,
            'path' => $path,
        ]);

        return redirect()->route('management-fee.edit', ['id' => $id])->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Mengupdate lampiran di database.
     */
    public function update(Request $request, $document_id, $docdetail_id)
    {
        $request->validate([
            'file_name' => 'required|string|max:255',
        ]);

        $docdetail = ManfeeDocDetailPayments::where('document_id', $document_id)
            ->where('id', $docdetail_id)
            ->firstOrFail();

        $docdetail->update([
            'file_name' => $request->file_name,
        ]);

        return response()->json(['message' => 'Lampiran berhasil diperbarui.', 'data' => $docdetail]);
    }

    /**
     * Menghapus lampiran dari database berdasarkan ID dan document_id.
     */
    public function destroy($document_id, $docdetail_id)
    {
        $docdetail = ManfeeDocDetailPayments::where('document_id', $document_id)
            ->where('id', $docdetail_id)
            ->firstOrFail();

        $docdetail->delete();

        return redirect()->route('management-fee.edit', ['id' => $document_id])->with('success', 'Lampiran berhasil dihapus!');
    }
}
