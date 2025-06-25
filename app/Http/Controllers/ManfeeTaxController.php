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
            'file' => 'required|file|mimes:pdf|max:102400',
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
        ManfeeDocTax::create([
            'document_id' => $id,
            'file_name' => $request->file_name,
            'path' => $dropboxPath,
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
            'percentage' => 'required|numeric|min:0|max:100',
            'amount' => 'required|numeric|min:0',
        ]);

        $tax = ManfeeDocTax::where('document_id', $document_id)
            ->where('id', $tax_id)
            ->firstOrFail();

        $tax->update([
            'file_name' => $request->file_name,
            'percentage' => $request->percentage,
            'amount' => $request->amount,
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

        // 🔄 **Ambil path file dari database**
        $dropboxPath = $tax->path;

        // 🔥 **Panggil fungsi `delete()` dari `DropboxController` untuk hapus di Dropbox**
        $dropboxController = app(DropboxController::class);
        $dropboxController->deleteAttachment($dropboxPath);

        $tax->delete();

        return redirect()->route('management-fee.edit', ['id' => $document_id])->with('success', 'tax berhasil dihapus!');
    }
}
