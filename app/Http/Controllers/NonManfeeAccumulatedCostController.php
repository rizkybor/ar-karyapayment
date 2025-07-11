<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\NonManfeeDocument;
use App\Models\NonManfeeDocAccumulatedCost;

class NonManfeeAccumulatedCostController extends Controller
{
    /**
     * Menampilkan data Akumulasi Biaya berdasarkan ID dokumen.
     */
    public function show($id)
    {
        NonManfeeDocument::findOrFail($id);
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
    public function update(Request $request, $id, $accumulated_id = null)
    {
        // Validasi input dengan custom messages
        $request->validate([
            'accountId' => 'required|string|max:255',
            'akun' => 'required|string|max:255',
            'nama_akun' => 'required|string|max:255',
            'dpp_pekerjaan' => 'required|string|min:1',
            'rate_ppn' => 'required|numeric|min:11|max:99',
            'comment_ppn' => 'nullable|string|max:255',
            'billing_deadline' => 'nullable',
        ], [
            'akun.required' => 'Akun wajib diisi.',
            'akun.string' => 'Akun harus berupa teks.',
            'akun.max' => 'Akun tidak boleh lebih dari 255 karakter.',

            'dpp_pekerjaan.required' => 'DPP Pekerjaan harus diisi.',
            'dpp_pekerjaan.min' => 'DPP Pekerjaan tidak boleh kurang dari 0.',

            'rate_ppn.required' => 'Rate PPN harus diisi.',
            'rate_ppn.numeric' => 'Rate PPN harus berupa angka (gunakan titik untuk desimal).',
            'rate_ppn.min' => 'Rate PPN tidak boleh kurang dari 11%.',
            'rate_ppn.max' => 'Rate PPN tidak boleh lebih dari 99%.',
        ]);

        // Konversi format angka
        $dppPekerjaan = (float) str_replace('.', '', $request->dpp_pekerjaan);
        $ratePpn = (float) str_replace(',', '.', $request->rate_ppn);
        $nilaiPpn = round(($dppPekerjaan * $ratePpn) / 100, 2);
        $jumlah = $dppPekerjaan + $nilaiPpn;

        // Simpan/update data akumulasi
        $accumulatedCost = NonManfeeDocAccumulatedCost::updateOrCreate(
            [
                'id' => $accumulated_id,
                'document_id' => $id
            ],
            [
                'accountId' => $request->accountId,
                'account' => $request->akun,
                'account_name' => $request->nama_akun,
                'dpp' => $dppPekerjaan,
                'rate_ppn' => $ratePpn,
                'nilai_ppn' => $nilaiPpn,
                'comment_ppn' => $request->comment_ppn ?? '',
                'total' => $jumlah,
                'billing_deadline' => $request->billing_deadline,
            ]
        );

        return redirect()->route('non-management-fee.edit', ['id' => $id])
            ->with('success', 'Akumulasi Biaya berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $accumulatedCost = NonManfeeDocAccumulatedCost::where('document_id', $id)->first();

        if ($accumulatedCost) {
            $accumulatedCost->delete();
        }

        return redirect()->route('non-management-fee.edit', ['id' => $id])
            ->with('success', 'Akumulasi Biaya berhasil dihapus!');
    }
}
