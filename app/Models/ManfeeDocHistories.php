<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManfeeDocHistories extends Model
{
    use HasFactory;

    protected $table = 'manfee_doc_histories';

    protected $fillable = [
        'document_id',
        'performed_by',
        'role',
        'previous_status',
        'new_status',
        'action',
        'notes',
    ];
}
