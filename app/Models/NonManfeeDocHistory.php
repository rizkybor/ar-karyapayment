<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonManfeeDocHistory extends Model
{
    use HasFactory;

    protected $table = 'non_manfee_doc_histories';

    protected $fillable = [
        'document_id',
        'performed_by',
        'role',
        'previous_status',
        'new_status',
        'action',
        'notes',
    ];

    public function nonManfeeDocument()
    {
        return $this->belongsTo(NonManfeeDocument::class, 'document_id');
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}