<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ManfeeDocument extends Model
{
    use HasFactory;

    protected $table = 'manfee_documents';

    protected $fillable = [
        'contract_id',
        'invoice_number',
        'receipt_number',
        'letter_number',
        'manfee_bill',
        'period',
        'letter_subject',
        'category',
        'status',
        'last_reviewers',
        'is_active',
        'created_by',
    ];

    // Relasi ke Contracts
    public function contract()
    {
        return $this->belongsTo(Contracts::class, 'contract_id', 'id');
    }

    // Akses billType melalui contract
    public function billTypes()
    {
        return $this->contract->billType();
    }

    public function approvals()
    {
        return $this->morphMany(DocumentApproval::class, 'document');
    }

    public function latestApproval()
    {
        return $this->morphOne(DocumentApproval::class, 'document')->latestOfMany();
    }

    // Edit
    public function detailPayments()
    {
        return $this->hasMany(ManfeeDocDetailPayments::class, 'document_id');
    }

    public function accumulatedCosts()
    {
        return $this->hasMany(ManfeeDocAccumulatedCost::class, 'document_id');
    }

    public function attachments()
    {
        return $this->hasMany(ManfeeDocAttachments::class, 'document_id');
    }

    public function descriptions()
    {
        return $this->hasMany(ManfeeDocDescriptions::class, 'document_id');
    }

    public function taxFiles()
    {
        return $this->hasMany(ManfeeDocTax::class, 'document_id');
    }

    public function histories()
    {
        return $this->hasMany(ManfeeDocHistories::class, 'document_id');
    }
}
