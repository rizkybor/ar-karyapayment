<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NonManfeeDocDescription;

class NonManfeeDescriptionController extends Controller
{
    /**
     * Menampilkan detail deskripsi berdasarkan ID dan document_id.
     */
    public function show($document_id, $description_id)
    {
        $description = NonManfeeDocDescription::where('document_id', $document_id)
                            ->where('id', $description_id)
                            ->firstOrFail();

        return response()->json($description);
    }

    /**
     * Menyimpan deskripsi baru ke database.
     */
    public function store(Request $request, $document_id)
    {
        $request->validate([
            'description' => 'required|string|max:500',
        ]);

        $description = NonManfeeDocDescription::create([
            'document_id' => $document_id,
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Deskripsi berhasil ditambahkan.', 'data' => $description]);
    }

    /**
     * Mengupdate deskripsi di database.
     */
    public function update(Request $request, $document_id, $description_id)
    {
        $request->validate([
            'description' => 'required|string|max:500',
        ]);

        $description = NonManfeeDocDescription::where('document_id', $document_id)
                            ->where('id', $description_id)
                            ->firstOrFail();

        $description->update([
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Deskripsi berhasil diperbarui.', 'data' => $description]);
    }

    /**
     * Menghapus deskripsi dari database berdasarkan ID dan document_id.
     */
    public function destroy($document_id, $description_id)
    {
        $description = NonManfeeDocDescription::where('document_id', $document_id)
                            ->where('id', $description_id)
                            ->firstOrFail();

        $description->delete();

        return response()->json(['message' => 'Deskripsi berhasil dihapus.']);
    }
}