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
            'file' => 'required|file|max:10240',
        ]);

        // **📂 Ambil File dari Request**
        $file = $request->file('file');
        $fileName = $request->file_name;
        $dropboxFolderName = '/taxes/';

        // 🚀 **Panggil fungsi uploadAttachment dari DropboxController**
        $dropboxController = new DropboxController();
        $dropboxPath = $dropboxController->uploadAttachment($file, $fileName, $dropboxFolderName);

        // ❌ Cek apakah upload ke Dropbox gagal
        if (!$dropboxPath) {
            return redirect()->route('non-management-fee.edit', ['id' => $id])
                ->with('error', 'Gagal mengunggah file.');
        }

        // Simpan ke database
        NonManfeeDocTax::create([
            'document_id' => $id,
            'file_name' => $fileName,
            'path' => $dropboxPath,
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

        // 🔄 **Ambil path file dari database**
        $dropboxPath = $tax->path;

        // 🔥 **Panggil fungsi `delete()` dari `DropboxController` untuk hapus di Dropbox**
        $dropboxController = app(DropboxController::class);
        $dropboxController->deleteAttachment($dropboxPath);

        $tax->delete();

        return redirect()->route('non-management-fee.edit', ['id' => $id])->with('success', 'Tax berhasil dihapus!');
    }
}