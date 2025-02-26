<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NonManfeeDocAccumulatedCost;

class NonManfeeAccumulatedCostController extends Controller
{
    /**
     * Menampilkan detail biaya berdasarkan document_id dan accumulated_cost_id.
     */
    public function show($document_id, $accumulated_cost_id)
    {
        $accumulatedCost = NonManfeeDocAccumulatedCost::where('document_id', $document_id)
                            ->where('id', $accumulated_cost_id)
                            ->firstOrFail();

        return response()->json([
            'message' => 'Data ditemukan',
            'data' => $accumulatedCost
        ]);
    }

    /**
     * Menyimpan biaya baru ke database.
     */
    public function store(Request $request, $document_id)
    {
        $request->validate([
            'account' => 'required|string|max:255',
            'dpp' => 'required|numeric|min:0',
            'rate_ppn' => 'required|numeric|min:0|max:100',
            'nilai_ppn' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        $accumulatedCost = NonManfeeDocAccumulatedCost::create([
            'document_id' => $document_id,
            'account' => $request->account,
            'dpp' => $request->dpp,
            'rate_ppn' => $request->rate_ppn,
            'nilai_ppn' => $request->nilai_ppn,
            'total' => $request->total,
        ]);

        return response()->json([
            'message' => 'Biaya terakumulasi berhasil ditambahkan.',
            'data' => $accumulatedCost
        ], 201);
    }

    /**
     * Mengupdate biaya di database.
     */
    public function update(Request $request, $document_id, $accumulated_cost_id)
    {
        $request->validate([
            'account' => 'required|string|max:255',
            'dpp' => 'required|numeric|min:0',
            'rate_ppn' => 'required|numeric|min:0|max:100',
            'nilai_ppn' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        $accumulatedCost = NonManfeeDocAccumulatedCost::where('document_id', $document_id)
                            ->where('id', $accumulated_cost_id)
                            ->firstOrFail();

        $accumulatedCost->update($request->all());

        return response()->json([
            'message' => 'Biaya terakumulasi berhasil diperbarui.',
            'data' => $accumulatedCost
        ]);
    }

    /**
     * Menghapus biaya dari database berdasarkan document_id dan accumulated_cost_id.
     */
    public function destroy($document_id, $accumulated_cost_id)
    {
        $accumulatedCost = NonManfeeDocAccumulatedCost::where('document_id', $document_id)
                            ->where('id', $accumulated_cost_id)
                            ->firstOrFail();
        
        $accumulatedCost->delete();

        return response()->json([
            'message' => 'Biaya terakumulasi berhasil dihapus.'
        ]);
    }
}