<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NonManfeeDocAttachment;

class NonManfeeAttachmentController extends Controller
{
    /**
     * Menampilkan detail lampiran berdasarkan ID dan id.
     */
    public function show($id, $attachment_id)
    {
        $attachment = NonManfeeDocAttachment::where('id', $id)
            ->where('id', $attachment_id)
            ->firstOrFail();

        return response()->json($attachment);
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


        NonManfeeDocAttachment::create([
            'document_id' => $id,
            'file_name' => $request->file_name,
            'path' => $path,
        ]);

        return redirect()->route('non-management-fee.edit', ['id' => $id])->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Mengupdate lampiran di database.
     */
    public function update(Request $request, $id, $attachment_id)
    {
        $request->validate([
            'file_name' => 'required|string|max:255',
        ]);

        $attachment = NonManfeeDocAttachment::where('id', $id)
            ->where('id', $attachment_id)
            ->firstOrFail();

        $attachment->update([
            'file_name' => $request->file_name,
        ]);

        return response()->json(['message' => 'Lampiran berhasil diperbarui.', 'data' => $attachment]);
    }

    /**
     * Menghapus lampiran dari database berdasarkan ID dan id.
     */
    public function destroy($id, $attachment_id)
    {
        $attachment = NonManfeeDocAttachment::where('document_id', $id)
            ->where('id', $attachment_id)
            ->firstOrFail();

        $attachment->delete();

        return redirect()->route('non-management-fee.edit', ['id' => $id])->with('success', 'Attachment berhasil dihapus!');
    }
}
