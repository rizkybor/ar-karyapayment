<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonManfeeDocDetailPayments extends Model
{
    use HasFactory;

    protected $table = 'non_manfee_doc_detail_payments';

    protected $fillable = [
        'document_id',
        'expense_type',
        'account',
        'account_name',
        'accountId',
        'nilai_biaya',
    ];

    public function NonManfeeDocument()
    {
        return $this->belongsTo(NonManfeeDocument::class, 'document_id');
    }
}
