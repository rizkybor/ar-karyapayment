<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NonManfeeDocDescription;

class NonManfeeDescriptionController extends Controller
{
    /**
     * Menampilkan detail deskripsi berdasarkan ID dokumen dan ID deskripsi.
     */
    public function show($id, $description_id)
    {
        $description = NonManfeeDocDescription::where('document_id', $id)
                            ->where('id', $description_id)
                            ->firstOrFail();

        return response()->json($description);
    }

    /**
     * Menyimpan deskripsi baru ke database.
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:500',
        ]);

        $description = NonManfeeDocDescription::create([
            'document_id' => $id,
            'description' => $request->description,
        ]);

        return redirect()->route('non-management-fee.edit', ['id' => $id])
                         ->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Mengupdate deskripsi di database.
     */
    public function update(Request $request, $id, $description_id)
    {
        $request->validate([
            'description' => 'required|string|max:500',
        ]);

        $description = NonManfeeDocDescription::where('document_id', $id)
                            ->where('id', $description_id)
                            ->firstOrFail();

        $description->update([
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Deskripsi berhasil diperbarui.',
            'data' => $description
        ]);
    }

    /**
     * Menghapus deskripsi dari database berdasarkan ID dokumen dan ID deskripsi.
     */
    public function destroy($id, $description_id)
    {
        $description = NonManfeeDocDescription::where('document_id', $id)
                            ->where('id', $description_id)
                            ->firstOrFail();

        $description->delete();

        return redirect()->route('non-management-fee.edit', ['id' => $id])
                         ->with('success', 'Deskripsi berhasil dihapus!');
    }
}