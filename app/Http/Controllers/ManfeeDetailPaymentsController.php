<?php

namespace App\Http\Controllers;

use App\Models\ManfeeDocDetailPayments;
use Illuminate\Http\Request;

class ManfeeDetailPaymentsController extends Controller
{
    public function show($document_id, $docdetail_id)
    {
        $docdetail = ManfeeDocDetailPayments::where('document_id', $document_id)
            ->where('id', $docdetail_id)
            ->firstOrFail();

        return response()->json($docdetail);
    }

    /**
     * Menyimpan lampiran baru ke database.
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'expense_type' => 'required',
            'account' => 'required',
            'account_name' => 'required',
            'uraian' => 'required',
            'nilai_biaya' => 'required',
        ]);

        // dd($request->all());

        $rupiahBiaya = (float) str_replace('.', '', $request->nilai_biaya);

        // Simpan ke database
        ManfeeDocDetailPayments::create([
            'document_id' => $id,
            'expense_type' => $request->expense_type,
            'account' => $request->account,
            'account_name' => $request->account_name,
            'uraian' => $request->uraian,
            'nilai_biaya' => $rupiahBiaya,
        ]);

        return redirect()->route('management-fee.edit', ['id' => $id])->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Mengupdate lampiran di database.
     */
    public function update(Request $request, $document_id, $docdetail_id)
    {
        $request->validate([
            'expense_type' => 'required',
            'account' => 'required',
            'uraian' => 'required',
            'nilai_biaya' => 'required',
        ]);

        $rupiahBiaya = (float) str_replace('.', '', $request->nilai_biaya);

        $docdetail = ManfeeDocDetailPayments::where('document_id', $document_id)
            ->where('id', $docdetail_id)
            ->firstOrFail();

        $docdetail->update([
            'expense_type' => $request->expense_type,
            'account' => $request->account,
            'uraian' => $request->uraian,
            'nilai_biaya' => $rupiahBiaya,
        ]);

        return redirect()->route('management-fee.edit', ['id' => $document_id])->with('success', 'Data berhasil diperbaharui!');
    }

    /**
     * Menghapus lampiran dari database berdasarkan ID dan document_id.
     */
    public function destroy($document_id, $docdetail_id)
    {
        $docdetail = ManfeeDocDetailPayments::where('document_id', $document_id)
            ->where('id', $docdetail_id)
            ->firstOrFail();

        $docdetail->delete();

        return redirect()->route('management-fee.edit', ['id' => $document_id])->with('success', 'Lampiran berhasil dihapus!');
    }
}
