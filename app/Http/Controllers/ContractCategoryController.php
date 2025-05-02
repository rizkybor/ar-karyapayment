<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ContractCategory;

class ContractCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $categories = ContractCategory::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate($perPage);

        return view('pages/settings/contract-categories/index', compact('categories', 'perPage', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages/settings/contract-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:contract_categories,name|max:255',
        ]);

        ContractCategory::create($request->only('name'));

        return redirect()->route('contract-categories.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ContractCategory $contractCategory)
    {
        return view('pages/settings/contract-categories.show', [
            'category' => $contractCategory
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContractCategory $contractCategory)
    {
        return view('pages/settings/contract-categories/edit', [
            'category' => $contractCategory
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContractCategory $contractCategory)
    {
        $request->validate([
            'name' => 'required|max:255|unique:contract_categories,name,' . $contractCategory->id,
        ]);

        $contractCategory->update($request->only('name'));

        return redirect()->route('contract-categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractCategory $contractCategory)
    {
        $contractCategory->delete();

        return redirect()->route('contract-categories.index')->with('success', 'Kategori berhasil dihapus!');
    }
}