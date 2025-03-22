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
        // Debugging untuk melihat data yang dikirim
        // dd($request->all());

        // Validasi input dengan custom messages
        $request->validate([
            'akun' => 'required|string|max:255',
            'dpp_pekerjaan' => 'required|string|min:1', // String karena ada format angka dengan titik
            'rate_ppn' => 'required|numeric|min:0|max:999.99',
        ], [
            'akun.required' => 'Akun wajib diisi.',
            'akun.string' => 'Akun harus berupa teks.',
            'akun.max' => 'Akun tidak boleh lebih dari 255 karakter.',

            'dpp_pekerjaan.required' => 'DPP Pekerjaan harus diisi.',
            'dpp_pekerjaan.min' => 'DPP Pekerjaan tidak boleh kurang dari 0.',

            'rate_ppn.required' => 'Rate PPN harus diisi.',
            'rate_ppn.numeric' => 'Rate PPN harus berupa angka (gunakan titik untuk desimal).',
            'rate_ppn.min' => 'Rate PPN tidak boleh kurang dari 0.',
            'rate_ppn.max' => 'Rate PPN tidak boleh lebih dari 999.99.',
        ]);

        // **Konversi format angka untuk penyimpanan ke database**
        $dppPekerjaan = (float) str_replace('.', '', $request->dpp_pekerjaan); // Hilangkan titik dari format rupiah
        $ratePpn = (float) str_replace(',', '.', $request->rate_ppn); // Ubah koma menjadi titik untuk desimal
        $nilaiPpn = round(($dppPekerjaan * $ratePpn) / 100, 2); // Hitung nilai PPN
        $jumlah = $dppPekerjaan + $nilaiPpn; // Total nilai

        // Cek apakah `accumulated_id` ada, jika ada update, jika tidak buat baru
        $accumulatedCost = NonManfeeDocAccumulatedCost::updateOrCreate(
            [
                'id' => $accumulated_id, // Jika `accumulated_id` ada, update. Jika tidak, buat baru
                'document_id' => $id
            ],
            [
                'account' => $request->akun,
                'dpp' => $dppPekerjaan,
                'rate_ppn' => $ratePpn,
                'nilai_ppn' => $nilaiPpn,
                'total' => $jumlah,
            ]
        );

        // Redirect ke halaman edit dengan pesan sukses
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
