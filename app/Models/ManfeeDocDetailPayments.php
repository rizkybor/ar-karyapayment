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
        'total_expense',
    ];
}
