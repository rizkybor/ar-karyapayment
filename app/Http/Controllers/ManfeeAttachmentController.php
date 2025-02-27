<?php

namespace App\Http\Controllers;

use App\Models\ManfeeDocAttachments;
use Illuminate\Http\Request;

class ManfeeAttachmentController extends Controller
{
    /**
     * Menampilkan detail lampiran berdasarkan ID dan document_id.
     */
    public function show($document_id, $attachment_id)
    {
        $attachment = ManfeeDocAttachments::where('document_id', $document_id)
            ->where('id', $attachment_id)
            ->firstOrFail();

        return response()->json($attachment);
    }

    /**
     * Menyimpan lampiran baru ke database.
     */
    public function store(Request $request, $id)
    {
        logger('Request data:', $request->all());
        logger('Document ID:', ['id' => $id]);
        $request->validate([
            'file_name' => 'required|string|max:255',
            'file' => 'required|file|max:2048',
        ]);

        // Simpan file dan ambil path-nya
        $path = $request->file('file')->store('attachments', 'public');

        // Simpan ke database
        ManfeeDocAttachments::create([
            'document_id' => $id,
            'file_name' => $request->file_name,
            'path' => $path,
        ]);

        return redirect()->route('management-fee.edit', ['id' => $id])->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Mengupdate lampiran di database.
     */
    public function update(Request $request, $document_id, $attachment_id)
    {
        $request->validate([
            'file_name' => 'required|string|max:255',
        ]);

        $attachment = ManfeeDocAttachments::where('document_id', $document_id)
            ->where('id', $attachment_id)
            ->firstOrFail();

        $attachment->update([
            'file_name' => $request->file_name,
        ]);

        return response()->json(['message' => 'Lampiran berhasil diperbarui.', 'data' => $attachment]);
    }

    /**
     * Menghapus lampiran dari database berdasarkan ID dan document_id.
     */
    public function destroy($document_id, $attachment_id)
    {
        $attachment = ManfeeDocAttachments::where('document_id', $document_id)
            ->where('id', $attachment_id)
            ->firstOrFail();

        $attachment->delete();

        return redirect()->route('management-fee.edit', ['id' => $document_id])->with('success', 'Lampiran berhasil dihapus!');
    }
}
