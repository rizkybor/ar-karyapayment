<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManfeeDocTax extends Model
{
    use HasFactory;

    protected $table = 'manfee_doc_tax';

    protected $fillable = [
        'document_id',
        'file_name',
        'path',
    ];
}
