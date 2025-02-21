<?php

namespace App\Http\Controllers;

use App\Models\ManfeeDocument;
use Illuminate\Http\Request;

class ManfeeDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages/ar-menu/management-fee/index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages/ar-menu/management-fee/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ManfeeDocument $manfeeDocument)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ManfeeDocument $manfeeDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ManfeeDocument $manfeeDocument)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ManfeeDocument $manfeeDocument)
    {
        //
    }
}
