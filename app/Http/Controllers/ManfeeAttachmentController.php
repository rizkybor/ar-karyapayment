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
        $request->validate([
            'file_name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        // **📂 Ambil File dari Request**
        $file = $request->file('file');
        $fileName = $request->file_name;
        $dropboxFolderName = '/attachments/';

        // 🚀 **Panggil fungsi uploadAttachment dari DropboxController**
        $dropboxController = new DropboxController();
        $dropboxPath = $dropboxController->uploadAttachment($file, $fileName, $dropboxFolderName);

        // ❌ Cek apakah upload ke Dropbox gagal
        if (!$dropboxPath) {
            return redirect()->route('management-fee.edit', ['id' => $id])
                ->with('error', 'Gagal mengunggah file.');
        }

        // ✅ Simpan data ke database dengan path Dropbox
        ManfeeDocAttachments::create([
            'document_id' => $id,
            'file_name' => $fileName,
            'path' => $dropboxPath,
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

        // 🔄 **Ambil path file dari database**
        $dropboxPath = $attachment->path;

        // 🔥 **Panggil fungsi `delete()` dari `DropboxController` untuk hapus di Dropbox**
        $dropboxController = app(DropboxController::class);
        $dropboxController->deleteAttachment($dropboxPath);

        $attachment->delete();

        return redirect()->route('management-fee.edit', ['id' => $document_id])->with('success', 'Lampiran berhasil dihapus!');
    }
}
