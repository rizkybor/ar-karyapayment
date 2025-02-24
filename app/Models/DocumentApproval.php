<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'document_type', // Polymorphic
        'approver_id',
        'role',
        'status',
        'comments',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Relasi ke model dokumen yang dapat berupa manfee_documents atau non_manfee_documents.
     */
    public function document(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relasi ke User yang melakukan approval.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}