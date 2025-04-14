<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $table = 'bank_accounts';

    protected $fillable = [
        'bank_name',
        'account_number',
        'account_name',
    ];

    /**
     * Relasi ke dokumen non-manfee (jika ingin digunakan)
     */
    public function documents()
    {
        return $this->hasMany(NonManfeeDocument::class, 'bank_account_id');
    }
}