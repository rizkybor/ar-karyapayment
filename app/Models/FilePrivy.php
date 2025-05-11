<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilePrivy extends Model
{
    use HasFactory;

    protected $table = 'file_privies';

    protected $fillable = [
        'document_id',
        'category_type',
        'type_document',
        'reference_number',
        'document_token',
        'status',
    ];

    public function manfeeDocument()
    {
        return $this->belongsTo(ManfeeDocument::class, 'document_id');
    }

    public function nonManfeeDocument()
    {
        return $this->belongsTo(NonManfeeDocument::class, 'document_id');
    }
}
