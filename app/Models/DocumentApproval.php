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
        'document_type',
        'approver_id',
        'approver_role',
        'submitter_id',
        'submitter_role',
        'status',
        'comments',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'status' => 'string',
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
     * Relasi ke User yang mengajukan approval.
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitter_id');
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
        return $query->where('approver_role', $role);
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

            '0'   => 'Draft',
            '1'   => 'Checked by Kepala Divisi',
            '2'   => 'Checked by Perbendaharaan',
            '3'   => 'Checked by Manager Keuangan',
            '4'   => 'Checked by Direktur Keuangan',
            '5'   => 'Checked by Pajak',
            '6'   => 'Done',
            '100' => 'Finished', // status belum digunakan
            '101' => 'Canceled', // status belum digunakan
            '102' => 'Revised',
            '103'  => 'Rejected',
        ];

        return $statuses[$this->status] ?? 'Unknown';
    }
}
