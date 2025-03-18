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
            'file' => 'required|file|max:10240', // Maksimal 10MB
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
            return redirect()->route('non-management-fee.edit', ['id' => $id])
                ->with('error', 'Gagal mengunggah file ke Dropbox.');
        }
    
        // ✅ Simpan data ke database dengan path Dropbox
        NonManfeeDocAttachment::create([
            'document_id' => $id,
            'file_name' => $fileName, // Simpan nama file yang diinput user
            'path' => $dropboxPath, // Simpan path dari Dropbox
        ]);
    
        return redirect()->route('non-management-fee.edit', ['id' => $id])
            ->with('success', 'File berhasil diunggah ke Dropbox dan disimpan!');
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

        // 🔄 **Ambil path file dari database**
        $dropboxPath = $attachment->path;
        dd($dropboxPath);
        // 🔥 **Panggil fungsi `delete()` dari `DropboxController` untuk hapus di Dropbox**
        $dropboxController = app(DropboxController::class);
        $dropboxController->deleteAttachment($dropboxPath);

        $attachment->delete();

        return redirect()->route('non-management-fee.edit', ['id' => $id])->with('success', 'Attachment berhasil dihapus!');
    }
}
