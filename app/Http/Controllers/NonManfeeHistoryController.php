<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NonManfeeDocHistory;

class NonManfeeHistoryController extends Controller
{
    /**
     * Menampilkan semua riwayat.
     */
    public function index()
    {
        $histories = NonManfeeDocHistory::latest()->get();

        return response()->json([
            'message' => 'Daftar riwayat berhasil diambil.',
            'data' => $histories
        ]);
    }

    /**
     * Menampilkan detail riwayat berdasarkan ID.
     */
    public function show($history_id)
    {
        $history = NonManfeeDocHistory::findOrFail($history_id);

        return response()->json([
            'message' => 'Detail riwayat ditemukan.',
            'data' => $history
        ]);
    }

    /**
     * Menyimpan riwayat baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:non_manfee_documents,id',
            'performed_by' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'previous_status' => 'nullable|string|max:255',
            'new_status' => 'required|string|max:255',
            'action' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $history = NonManfeeDocHistory::create($request->all());

        return response()->json([
            'message' => 'Riwayat berhasil ditambahkan.',
            'data' => $history
        ], 201);
    }

    /**
     * Menghapus riwayat berdasarkan ID.
     */
    public function destroy($history_id)
    {
        $history = NonManfeeDocHistory::findOrFail($history_id);
        $history->delete();

        return response()->json([
            'message' => 'Riwayat berhasil dihapus.'
        ]);
    }
}