<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManfeeDocHistories extends Model
{
    use HasFactory;

    protected $table = 'manfee_doc_histories';

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

    public function ManfeeDocument(): BelongsTo
    {
        return $this->belongsTo(ManfeeDocument::class, 'document_id');
    }


    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by')->withDefault([
            'name' => 'User Tidak Diketahui', // Default jika user dihapus
        ]);
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

    /**
     * Atribut custom untuk mendapatkan nama user dengan aman
     */
    public function getPerformedByNameAttribute()
    {
        return optional($this->performedBy)->name ?? 'User Tidak Diketahui';
    }

    /**
     * Atribut custom untuk memformat timestamp dengan baik
     */
    public function getFormattedTimestampAttribute()
    {
        return $this->created_at ? $this->created_at->format('d M Y, H:i') : '-';
    }
}
