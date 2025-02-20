<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterBillType extends Model
{
    use HasFactory;


    protected $table = 'mst_bill_type';

    protected $fillable = [
        'bill_type',
    ];
}
