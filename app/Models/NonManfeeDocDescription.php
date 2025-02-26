<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonManfeeDocDescription extends Model
{
    use HasFactory;

    protected $table = 'non_manfee_doc_descriptions';

    protected $fillable = [
        'document_id',
        'description',
    ];

    public function nonManfeeDocument()
    {
        return $this->belongsTo(NonManfeeDocument::class, 'document_id');
    }
}