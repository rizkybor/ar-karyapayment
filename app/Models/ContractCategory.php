<?php

// app/Models/ContractCategory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractCategory extends Model
{
    protected $fillable = ['name'];

    public function contracts()
    {
        return $this->hasMany(Contracts::class, 'category', 'name');
    }
}