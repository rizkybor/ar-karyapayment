<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonManfeeDocument extends Model
{
    use HasFactory;

    protected $table = 'non_manfee_documents';

    protected $fillable = [
        'contract_id',
        'invoice_number',
        'receipt_number',
        'letter_number',
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
}
