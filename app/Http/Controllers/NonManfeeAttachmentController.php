<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NonManfeeDocAttachment;

class NonManfeeAttachmentController extends Controller
{
    /**
     * Menampilkan detail lampiran berdasarkan ID dan document_id.
     */
    public function show($document_id, $attachment_id)
    {
        $attachment = NonManfeeDocAttachment::where('document_id', $document_id)
                            ->where('id', $attachment_id)
                            ->firstOrFail();

        return response()->json($attachment);
    }

    /**
     * Menyimpan lampiran baru ke database.
     */
    public function store(Request $request, $document_id)
    {
        $request->validate([
            'file_name' => 'required|string|max:255',
            'file' => 'required|file|max:2048',
        ]);

        $path = $request->file('file')->store('attachments');

        $attachment = NonManfeeDocAttachment::create([
            'document_id' => $document_id,
            'file_name' => $request->file_name,
            'path' => $path,
        ]);

        return response()->json(['message' => 'Lampiran berhasil ditambahkan.', 'data' => $attachment]);
    }

    /**
     * Mengupdate lampiran di database.
     */
    public function update(Request $request, $document_id, $attachment_id)
    {
        $request->validate([
            'file_name' => 'required|string|max:255',
        ]);

        $attachment = NonManfeeDocAttachment::where('document_id', $document_id)
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
        $attachment = NonManfeeDocAttachment::where('document_id', $document_id)
                            ->where('id', $attachment_id)
                            ->firstOrFail();

        $attachment->delete();

        return response()->json(['message' => 'Lampiran berhasil dihapus.']);
    }
}