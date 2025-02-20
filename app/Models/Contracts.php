<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contracts extends Model
{
    use HasFactory;

    protected $table = 'contracts';

    protected $fillable = [
        'contract_number',
        'employee_name',
        'value',
        'start_date',
        'end_date',
        'type',
        'path',
        'bill_type',
        'address',
        'work_unit',
        'status',
    ];

    public function manfeeDocuments()
    {
        return $this->hasMany(ManfeeDocument::class, 'contract_id', 'id');
    }
}
