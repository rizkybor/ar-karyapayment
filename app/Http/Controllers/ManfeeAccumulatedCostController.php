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

        $request->merge([
            'nilai_manfee' => (float) str_replace(',', '.', preg_replace('/[^\d,]/', '', $request->nilai_manfee)),
            'dpp' => (float) str_replace(',', '.', preg_replace('/[^\d,]/', '', $request->dpp)),
            'nilai_ppn' => (float) str_replace(',', '.', preg_replace('/[^\d,]/', '', $request->nilai_ppn)),
            'total' => (float) str_replace(',', '.', preg_replace('/[^\d,]/', '', $request->total)),
        ]);

        // Validasi input dengan custom messages
        $request->validate([
            'account' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'total_expense_manfee' => 'required|string|max:255',
            'nilai_manfee' => 'required|numeric',
            'dpp' => 'required|numeric',
            'rate_ppn' => 'required|numeric|min:0|max:999.99',
            'nilai_ppn' => 'required|numeric',
            'comment_ppn' => 'nullable|string|max:255',
            'total' => 'required|numeric',
        ]);

        // Ambil nilai dari request
        $account = $request->input('account');
        $account_name = $request->account_name;
        $totalExpenseManfee = $request->input('total_expense_manfee');
        $nilaiManfee = $request->input('nilai_manfee');
        $dpp = $request->input('dpp');
        $ratePpn = $request->input('rate_ppn');
        $nilaiPpn = $request->input('nilai_ppn');
        $total = $request->input('total');

        // Cek apakah `accumulated_id` ada, jika ada update, jika tidak buat baru
        ManfeeDocAccumulatedCost::updateOrCreate(
            [
                'id' => $accumulated_id,
                'document_id' => $id
            ],
            [
                'account' => $account,
                'account_name' => $account_name,
                'total_expense_manfee' => $totalExpenseManfee,
                'nilai_manfee' => $nilaiManfee,
                'dpp' => $dpp,
                'rate_ppn' => $ratePpn,
                'nilai_ppn' => $nilaiPpn,
                'comment_ppn' => $request->comment_ppn ?? '',
                'total' => $total,
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
