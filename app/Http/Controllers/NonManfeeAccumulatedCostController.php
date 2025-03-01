<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\NonManfeeDocAccumulatedCost;

class NonManfeeAccumulatedCostController extends Controller
{
    /**
     * Menampilkan detail biaya berdasarkan id dan accumulated_cost_id.
     */
    public function show($id, $accumulated_cost_id)
    {
        $accumulatedCost = NonManfeeDocAccumulatedCost::where('id', $id)
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
    public function store(Request $request, $id)
    {
        $request->validate([
            'account' => 'required|string|max:255',
            'dpp' => 'required|numeric|min:0',
            'rate_ppn' => 'required|numeric|min:0|max:100',
            'nilai_ppn' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        NonManfeeDocAccumulatedCost::create([
            'document_id' => $id,
            'account' => $request->account,
            'dpp' => $request->dpp,
            'rate_ppn' => $request->rate_ppn,
            'nilai_ppn' => $request->nilai_ppn,
            'total' => $request->total,
        ]);

        return redirect()->route('management-non-fee.edit', ['document_id' => $id])->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Mengupdate biaya di database.
     */
    public function update(Request $request, $id, $accumulated_cost_id)
    {
        $request->validate([
            'account' => 'required|string|max:255',
            'dpp' => 'required|numeric|min:0',
            'rate_ppn' => 'required|numeric|min:0|max:100',
            'nilai_ppn' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        $accumulatedCost = NonManfeeDocAccumulatedCost::where('id', $id)
                            ->where('id', $accumulated_cost_id)
                            ->firstOrFail();

        $accumulatedCost->update($request->all());

        return response()->json([
            'message' => 'Biaya terakumulasi berhasil diperbarui.',
            'data' => $accumulatedCost
        ]);
    }

    /**
     * Menghapus biaya dari database berdasarkan id dan accumulated_cost_id.
     */
    public function destroy($id, $accumulated_cost_id)
    {
        $accumulatedCost = NonManfeeDocAccumulatedCost::where('id', $id)
                            ->where('id', $accumulated_cost_id)
                            ->firstOrFail();
        
        $accumulatedCost->delete();

        return response()->json([
            'message' => 'Biaya terakumulasi berhasil dihapus.'
        ]);
    }
}