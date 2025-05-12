<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NationalDay extends Model
{
    protected $fillable = [
        'date_code',
        'title',
        'message',
        'icon',
    ];
}