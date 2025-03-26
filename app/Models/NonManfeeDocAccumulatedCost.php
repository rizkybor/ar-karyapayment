<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonManfeeDocAccumulatedCost extends Model
{
    use HasFactory;

    protected $table = 'non_manfee_doc_accumulated_costs';

    protected $fillable = [
        'document_id',
        'account',
        'account_name',
        'dpp',
        'rate_ppn',
        'nilai_ppn',
        'total',
    ];

    public function nonManfeeDocument()
    {
        return $this->belongsTo(NonManfeeDocument::class, 'document_id');
    }

    public function accumulatedCosts()
    {
        return $this->hasMany(NonManfeeDocAccumulatedCost::class, 'document_id');
    }
}