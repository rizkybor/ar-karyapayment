<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivyUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'privy_reference_number',
        'privy_register_token',
        'privy_id',
        'privy_channel_id',
        'privy_status',
        'privy_identity',
        'privy_reject_reason',
    ];

    protected $casts = [
        'privy_identity' => 'array',
        'privy_reject_reason' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


// Contoh penggunaan di controller saat pembubuhan privy :
$user = User::find($id);

if ($user->isPrivyVerified()) {
    // User adalah pengguna Privy dan sudah terverifikasi
} else {
    // Bukan pengguna Privy atau belum verified
}