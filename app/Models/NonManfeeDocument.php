<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NonManfeeDocument extends Model
{
    use HasFactory;

    protected $table = 'non_manfee_documents';

    protected $fillable = [
        'contract_id',
        'bank_account_id',
        'invoice_number',
        'receipt_number',
        'letter_number',
        'period',
        'letter_subject',
        'category',
        'status',
        'status_print',
        'reference_document',
        'reason_rejected',
        'path_rejected',
        'reason_amandemen',
        'path_amandemen',
        'last_reviewers',
        'is_active',
        'created_by',
        'expired_at',
    ];

    // ✅ Event Model untuk Set Expired_at & Auto-Update is_active
    protected static function boot()
    {
        parent::boot();

        // 1️⃣ Set Default Expired Date H+30 dengan Waktu 00:01:00 saat Dokumen Dibuat
        static::creating(function ($document) {
            $document->expired_at = Carbon::now()->addMonthNoOverflow()
                ->day(15)
                ->setTime(0, 1, 0);
        });

        // 2️⃣ Cek Apakah Dokumen Sudah Expired Setiap Kali Diambil dari Database
        static::retrieved(function ($document) {
            if ($document->expired_at && $document->expired_at < Carbon::now() && $document->is_active) {
                $document->update(['is_active' => 0]);
            }
        });
    }

    // Relasi ke User (pembuat dokumen)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke Contracts
    public function contract()
    {
        return $this->belongsTo(Contracts::class, 'contract_id', 'id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function approvals()
    {
        return $this->morphMany(DocumentApproval::class, 'document');
    }

    public function latestApproval()
    {
        return $this->morphOne(DocumentApproval::class, 'document')->latestOfMany();
    }

    // ✅ Relasi ke Non Manfee Doc Accumulated Costs
    public function accumulatedCosts()
    {
        return $this->hasMany(NonManfeeDocAccumulatedCost::class, 'document_id');
    }

    // ✅ Relasi ke Non Manfee Doc Attachments
    public function attachments()
    {
        return $this->hasMany(NonManfeeDocAttachment::class, 'document_id');
    }

    // ✅ Relasi ke Non Manfee Doc Descriptions
    public function descriptions()
    {
        return $this->hasMany(NonManfeeDocDescription::class, 'document_id');
    }

    // ✅ Relasi ke Non Manfee Doc Detail Payments
    public function detailPayments()
    {
        return $this->hasMany(NonManfeeDocDetailPayments::class, 'document_id');
    }

    // ✅ Relasi ke Non Manfee Doc Histories
    public function histories()
    {
        return $this->hasMany(NonManfeeDocHistory::class, 'document_id');
    }

    // ✅ Relasi ke Non Manfee Doc Tax
    public function taxFiles()
    {
        return $this->hasMany(NonManfeeDocTax::class, 'document_id');
    }

    // ✅ Relasi ke File Privy
    public function filePrivies()
    {
        return $this->hasMany(FilePrivy::class, 'document_id');
    }
}
