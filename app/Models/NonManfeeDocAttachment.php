<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonManfeeDocAttachment extends Model
{
    use HasFactory;

    protected $table = 'non_manfee_doc_attachments';

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