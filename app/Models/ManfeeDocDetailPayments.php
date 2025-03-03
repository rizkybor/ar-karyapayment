<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManfeeDocDetailPayments extends Model
{
    use HasFactory;

    protected $table = 'manfee_doc_detail_payments';

    protected $fillable = [
        'document_id',
        'expense_type',
        'account',
        'uraian',
        'nilai_biaya',
    ];

    public function ManfeeDocument()
    {
        return $this->belongsTo(ManfeeDocument::class, 'document_id');
    }
}
