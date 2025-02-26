<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonManfeeDocTax extends Model
{
    use HasFactory;

    protected $table = 'non_manfee_doc_tax';

    protected $fillable = [
        'document_id',
        'file_name',
        'path',
    ];

    public function nonManfeeDocument()
    {
        return $this->belongsTo(NonManfeeDocument::class, 'document_id');
    }
}