<?php

namespace App\Http\Controllers;

use App\Models\ManfeeDocAttachments;
use Illuminate\Http\Request;

class ManfeeAttachmentController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show($document_id, $attachment_id)
    {
        // Cari attachment berdasarkan document_id dan attachment_id
        $attachment = ManfeeDocAttachments::where('document_id', $document_id)
            ->where('id', $attachment_id)
            ->firstOrFail();

        return response()->json($attachment);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ManfeeDocAttachments $manfeeDocAttachments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ManfeeDocAttachments $manfeeDocAttachments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ManfeeDocAttachments $manfeeDocAttachments)
    {
        //
    }
}
