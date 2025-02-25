<?php

namespace App\Http\Controllers;

use App\Models\Contracts;
use App\Models\MasterBillType;
use App\Models\MasterType;
use App\Models\MasterWorkUnit;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContractsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contracts = Contracts::all();
        return view('pages/settings/contracts/index', compact('contracts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mstType = MasterType::all();
        $mstWorkUnit = MasterWorkUnit::all();
        return view('pages/settings/contracts/create', compact('mstType', 'mstWorkUnit'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'contract_number' => 'required',
            'employee_name' => 'required',
            'value' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'type' => 'required',
            'path' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'bill_type' => 'nullable|array',
            'address' => 'required',
            'work_unit' => 'required',
            'status' => 'nullable|in:0,1',
        ]);

        $input = $request->all();

        // Format Tanggal
        $input['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d');
        $input['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d');

        $input['status'] = isset($request->status) ? (int) $request->status : null;

        // Upload File
        if ($request->hasFile('path')) {
            $destinationPath = 'files/path/';
            $path = $request->file('path');
            $pathName = date('YmdHis') . '_' . uniqid() . '.' . $path->getClientOriginalExtension();
            $path->move(public_path($destinationPath), $pathName);
            $input['path'] = $pathName;
        } else {
            return redirect()->back()->with('error', 'Gambar wajib diunggah!');
        }

        // Simpan data kontrak ke database
        $contract = Contracts::create($input);

        // Simpan data bill_type hanya jika tipe kontrak adalah management_fee
        if ($input['type'] === 'management_fee' && !empty($request->bill_type)) {
            foreach ($request->bill_type as $billType) {
                MasterBillType::create([
                    'contract_id' => $contract->id,
                    'bill_type' => $billType,
                ]);
            }
        }

        return redirect()->route('contracts.index')->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contracts $contract)
    {
        $mstBillType = MasterBillType::where('contract_id', $contract->id)->get();
        $mstType = MasterType::all();
        $mstWorkUnit = MasterWorkUnit::all();
        return view('pages/settings/contracts/show', compact('contract', 'mstType', 'mstBillType', 'mstWorkUnit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contracts $contract)
    {
        $mstBillType = MasterBillType::where('contract_id', $contract->id)->get();
        $mstType = MasterType::all();
        $mstWorkUnit = MasterWorkUnit::all();
        return view('pages/settings/contracts/edit', compact('contract', 'mstType', 'mstBillType', 'mstWorkUnit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contracts $contract)
    {
        $request->validate([
            'contract_number' => 'required',
            'employee_name' => 'required',
            'value' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'type' => 'nullable',
            'path' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'bill_type' => 'nullable|array', // Ubah menjadi nullable
            'address' => 'required',
            'work_unit' => 'required',
            'status' => 'nullable|in:0,1',
        ]);

        $input = $request->except('path'); // Ambil semua kecuali file gambar

        $input['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d');
        $input['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d');
        $input['status'] = isset($request->status) ? (int) $request->status : null;

        if ($request->hasFile('path')) {
            // Hapus gambar lama jika ada
            if ($contract->path && file_exists(public_path('files/path/' . $contract->path))) {
                unlink(public_path('files/path/' . $contract->path));
            }

            // Simpan gambar baru
            $destinationPath = 'files/path/';
            $path = $request->file('path');
            $pathName = date('YmdHis') . '_' . uniqid() . '.' . $path->getClientOriginalExtension();
            $path->move(public_path($destinationPath), $pathName);
            $input['path'] = $pathName;
        }

        $contract->update($input);

        MasterBillType::where('contract_id', $contract->id)->delete();

        // Simpan data bill_type hanya jika tipe kontrak adalah management_fee
        if ($request->type === 'management_fee' && !empty($request->bill_type)) {
            foreach ($request->bill_type as $billType) {
                MasterBillType::create([
                    'contract_id' => $contract->id,
                    'bill_type' => $billType,
                ]);
            }
        }

        return redirect()->route('contracts.index')->with('success', 'Data berhasil diperbaharui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contracts $contract)
    {
        if ($contract->path && file_exists(public_path('files/path/' . $contract->path))) {
            unlink(public_path('files/path/' . $contract->path));
        }

        $contract->delete();

        return redirect()->route('contracts.index')->with('success', 'Data berhasil dihapus!');
    }
}
