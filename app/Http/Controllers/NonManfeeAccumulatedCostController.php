<?php

namespace App\Http\Controllers;

use App\Models\NonManfeeDocument;
use App\Models\NonManfeeDocAccumulatedCost;
use Illuminate\Http\Request;

class NonManfeeAccumulatedCostController extends Controller
{
    /**
     * Menampilkan data Akumulasi Biaya berdasarkan ID dokumen.
     */
    public function show($id)
    {
        $nonManfeeDocument = NonManfeeDocument::findOrFail($id);
        $accumulatedCost = NonManfeeDocAccumulatedCost::where('document_id', $id)->first();

        return response()->json([
            'akun' => $accumulatedCost->account ?? null,
            'dpp_pekerjaan' => $accumulatedCost->dpp ?? 0,
            'rate_ppn' => $accumulatedCost->rate_ppn ?? 0,
            'nilai_ppn' => $accumulatedCost->nilai_ppn ?? 0,
            'jumlah' => $accumulatedCost->total ?? 0,
        ]);
    }

    /**
     * Menyimpan atau memperbarui data Akumulasi Biaya.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'akun' => 'required|string|max:255',
            'dpp_pekerjaan' => 'required|numeric|min:0',
            'rate_ppn' => 'required|integer|min:0|max:999',
        ]);

        // Konversi nilai agar bisa diproses dalam database
        $dppPekerjaan = (float) str_replace('.', '', $request->dpp_pekerjaan);
        $ratePpn = (int) $request->rate_ppn;
        $nilaiPpn = ($dppPekerjaan * $ratePpn) / 100;
        $jumlah = $dppPekerjaan + $nilaiPpn;

        // Simpan ke tabel akumulasi biaya
        $accumulatedCost = NonManfeeDocAccumulatedCost::updateOrCreate(
            ['document_id' => $id],
            [
                'account' => $request->akun,
                'dpp' => $dppPekerjaan,
                'rate_ppn' => $ratePpn,
                'nilai_ppn' => $nilaiPpn,
                'total' => $jumlah,
            ]
        );

        return redirect()->route('management-non-fee.edit', ['id' => $id])
            ->with('success', 'Akumulasi Biaya berhasil diperbarui!');
    }
    
    public function destroy($id)
    {
        $accumulatedCost = NonManfeeDocAccumulatedCost::where('document_id', $id)->first();
    
        if ($accumulatedCost) {
            $accumulatedCost->delete();
        }
    
        return redirect()->route('management-non-fee.edit', ['id' => $id])
            ->with('success', 'Akumulasi Biaya berhasil dihapus!');
    }
}