<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'status' => 'string', // Status disimpan sebagai string yang berisi angka
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

    /**
     * Relasi ke tabel notifications untuk menyimpan notifikasi terkait approval ini.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'notifiable_id')->where('notifiable_type', self::class);
    }

    /**
     * Scope untuk mengambil approval berdasarkan status tertentu.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk mengambil approval berdasarkan role tertentu.
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope untuk mengambil approval berdasarkan user tertentu.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('approver_id', $userId);
    }

    /**
     * Konversi status angka menjadi teks (Draft, Approved, dll)
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            '0' => 'Draft',
            '1' => 'Pending at Kepala Divisi',
            '2' => 'Pending at Pembendaharaan',
            '3' => 'Pending at Manager Keuangan',
            '4' => 'Pending at Direktur Keuangan',
            '5' => 'Pending at Pajak',
            '6' => 'Close Doc at Pembendaharaan',
            '9' => 'Requires Information',
            '99' => 'Rejected',
        ];

        return $statuses[$this->status] ?? 'Unknown';
    }
}