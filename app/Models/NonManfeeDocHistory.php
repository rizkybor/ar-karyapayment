<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NonManfeeDocHistory extends Model
{
    use HasFactory;

    protected $table = 'non_manfee_doc_histories';

    protected $fillable = [
        'document_id',
        'performed_by',
        'role',
        'previous_status',
        'new_status',
        'action',
        'notes',
    ];

    protected $casts = [
        'previous_status' => 'string',
        'new_status'      => 'string',
        'action'          => 'string',
        'notes'           => 'string',
    ];

    /**
     * Relasi ke NonManfeeDocument
     */
    public function nonManfeeDocument(): BelongsTo
    {
        return $this->belongsTo(NonManfeeDocument::class, 'document_id');
    }

    /**
     * Relasi ke User yang melakukan aksi
     */
    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Scope untuk filter berdasarkan dokumen tertentu
     */
    public function scopeByDocument($query, $documentId)
    {
        return $query->where('document_id', $documentId);
    }

    /**
     * Scope untuk filter berdasarkan user yang melakukan aksi
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('performed_by', $userId);
    }

    /**
     * Scope untuk filter berdasarkan action tertentu (approve, reject, revise, dll)
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }
}