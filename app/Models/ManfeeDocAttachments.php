<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManfeeDocAttachments extends Model
{
    use HasFactory;

    protected $table = 'manfee_doc_attachments';

    protected $fillable = [
        'document_id',
        'file_name',
        'path',
    ];
}
