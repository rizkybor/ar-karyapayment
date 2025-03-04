<?php

namespace App\Http\Controllers;

use App\Models\ManfeeDocument;
use App\Models\ManfeeDocAccumulatedCost;
use Illuminate\Http\Request;

class ManfeeAccumulatedCostController extends Controller
{
    /**
     * Menampilkan data Akumulasi Biaya berdasarkan ID dokumen.
     */
    public function show($id)
    {
        $ManfeeDocument = ManfeeDocument::findOrFail($id);
        $accumulatedCost = ManfeeDocAccumulatedCost::where('document_id', $id)->first();

        return response()->json([
            'account' => $accumulatedCost->account ?? null,
            'dpp' => $accumulatedCost->dpp ?? 0,
            'rate_ppn' => $accumulatedCost->rate_ppn ?? 0,
            'nilai_manfee' => $accumulatedCost->nilai_manfee ?? 0,
            'total_expense_manfee'  => $accumulatedCost->total_expense_manfee ?? 0,
            'nilai_ppn' => $accumulatedCost->nilai_ppn ?? 0,
            'total' => $accumulatedCost->total ?? 0,
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
            'total_expense_manfee' => 'required|string|max:255',
            'rate_ppn' => 'required|numeric|min:0|max:999.99',

        ], [
            'akun.required' => 'Akun wajib diisi.',
            'akun.string' => 'Akun harus berupa teks.',
            'akun.max' => 'Akun tidak boleh lebih dari 255 karakter.',

            'expense_manfee.required' => 'Rate Manfee harus diisi.',
            'expense_manfee.min' => 'Rate Manfee tidak boleh kurang dari 0.',

            'rate_ppn.required' => 'Rate PPN harus diisi.',
            'rate_ppn.numeric' => 'Rate PPN harus berupa angka (gunakan titik untuk desimal).',
            'rate_ppn.min' => 'Rate PPN tidak boleh kurang dari 0.',
            'rate_ppn.max' => 'Rate PPN tidak boleh lebih dari 999.99.',
        ]);

        $nilai_manfee = $subtotals * $total_expense_manfee;
        $dpp = $nilai_manfee + $subtotalBiayaNonPersonil;
        $nilai_ppn =
            jumlah =

            // **Konversi format angka untuk penyimpanan ke database**
            $dppPekerjaan = (float) str_replace('.', '', $request->dpp_pekerjaan); // Hilangkan titik dari format rupiah
        $ratePpn = (float) str_replace(',', '.', $request->rate_ppn); // Ubah koma menjadi titik untuk desimal
        $nilaiPpn = round(($dppPekerjaan * $ratePpn) / 100, 2); // Hitung nilai PPN
        $jumlah = $dppPekerjaan + $nilaiPpn; // Total nilai

        // Cek apakah `accumulated_id` ada, jika ada update, jika tidak buat baru
        $accumulatedCost = ManfeeDocAccumulatedCost::updateOrCreate(
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
        return redirect()->route('management-fee.edit', ['id' => $id])
            ->with('success', 'Akumulasi Biaya berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $accumulatedCost = ManfeeDocAccumulatedCost::where('document_id', $id)->first();

        if ($accumulatedCost) {
            $accumulatedCost->delete();
        }

        return redirect()->route('management-fee.edit', ['id' => $id])
            ->with('success', 'Akumulasi Biaya berhasil dihapus!');
    }
}
