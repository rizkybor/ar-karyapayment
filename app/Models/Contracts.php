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
        'contract_initial',
        'title',
        'category',
        'employee_name',
        'value',
        'contract_date',
        'start_date',
        'end_date',
        'type',
        'path',
        'address',
        'work_unit',
        'departmentId',
        'projectId',
        'segmenUsahaId',
        'status',
    ];

    // one to many
    public function manfeeDocuments()
    {
        return $this->hasMany(ManfeeDocument::class, 'contract_id', 'id');
    }

    public function nonManfeeDocuments()
    {
        return $this->hasMany(NonManfeeDocument::class, 'contract_id', 'id');
    }

    // one to one
    public function billTypes()
    {
        return $this->hasMany(MasterBillType::class, 'contract_id', 'id');
    }
}
