<?php

namespace App\Http\Controllers;

use App\Models\Contracts;
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
        return view('pages/settings/contracts/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'contract_number' => 'required',
            'employee_name' => 'required',
            'value' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'type' => 'required',
            'path' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'bill_type' => 'required',
            'address' => 'required',
            'work_unit' => 'required',
            'status' => 'required|in:0,1',
        ]);

        $input = $request->all();

        $input['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d');
        $input['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d');

        $input['status'] = (int) $request->status;

        if ($request->hasFile('path')) {
            $destinationPath = 'files/path/';
            $path = $request->file('path');
            $pathName = date('YmdHis') . '_' . uniqid() . '.' . $path->getClientOriginalExtension();
            $path->move(public_path($destinationPath), $pathName);
            $input['path'] = $pathName;
        } else {
            return redirect()->back()->with('error', 'Gambar wajib diunggah!');
        }

        Contracts::create($input);

        return redirect()->route('contracts.index')->with('success', 'Data berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contracts $contracts)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contracts $contracts)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contracts $contracts)
    {
        //
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
