<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManfeeDocAccumulatedCost extends Model
{
    use HasFactory;

    protected $table = 'manfee_doc_accumulated_costs';

    protected $fillable = [
        'document_id',
        'accountId',
        'account',
        'account_name',
        'total_expense_manfee',
        'nilai_manfee',
        'dpp',
        'rate_ppn',
        'nilai_ppn',
        'comment_ppn',
        'total',
    ];

    public function ManfeeDocument()
    {
        return $this->belongsTo(ManfeeDocument::class, 'document_id');
    }
}
