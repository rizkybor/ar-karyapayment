<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterType extends Model
{
  use HasFactory;

  protected $table = 'mst_type';

  protected $fillable = [
    'type',
  ];
}
