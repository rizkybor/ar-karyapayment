<?php

namespace App\Http\Controllers;

use App\Models\ManfeeDocDescriptions;
use Illuminate\Http\Request;

class ManfeeDescriptionsController extends Controller
{
    public function show($document_id, $descriptions_id)
    {
        $descriptions = ManfeeDocDescriptions::where('document_id', $document_id)
            ->where('id', $descriptions_id)
            ->firstOrFail();

        return response()->json($descriptions);
    }

    /**
     * Menyimpan deskripsi baru ke database.
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string',
        ]);

        // Simpan ke database
        ManfeeDocDescriptions::create([
            'document_id' => $id,
            'description' => $request->description,
        ]);

        return redirect()->route('management-fee.edit', ['id' => $id])->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Mengupdate deskripsi di database.
     */
    public function update(Request $request, $document_id, $descriptions_id)
    {
        $request->validate([
            'description' => 'required|string',
        ]);

        $descriptions = ManfeeDocDescriptions::where('document_id', $document_id)
            ->where('id', $descriptions_id)
            ->firstOrFail();

        $descriptions->update([
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Deskripsi berhasil diperbarui.',
            'data' => $descriptions
        ]);
    }

    /**
     * Menghapus deskripsi dari database berdasarkan ID dan document_id.
     */
    public function destroy($document_id, $descriptions_id)
    {
        $descriptions = ManfeeDocDescriptions::where('document_id', $document_id)
            ->where('id', $descriptions_id)
            ->firstOrFail();

        $descriptions->delete();

        return redirect()->route('management-fee.edit', ['id' => $document_id])->with('success', 'deskripsi berhasil dihapus!');
    }
}
