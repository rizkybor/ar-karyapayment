<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManfeeDocDescriptions extends Model
{
    use HasFactory;

    protected $table = 'manfee_doc_descriptions';

    protected $fillable = [
        'document_id',
        'description',
    ];
}
