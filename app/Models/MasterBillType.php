<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterBillType extends Model
{
    use HasFactory;


    protected $table = 'mst_bill_type';

    protected $fillable = [
        'contract_id',
        'bill_type',
    ];

    public function contract()
    {
        return $this->belongsTo(Contracts::class);
    }
}
