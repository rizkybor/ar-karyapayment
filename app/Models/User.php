<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nip',
        'department',
        'position',
        'role',
        'employee_status',
        'gender',
        'identity_number',
        'signature',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Relasi ke approval sebagai approver.
     */
    public function approvals()
    {
        return $this->hasMany(DocumentApproval::class, 'approver_id');
    }

    /**
     * Relasi ke approval sebagai submitter (pengaju approval).
     */
    public function submittedApprovals()
    {
        return $this->hasMany(DocumentApproval::class, 'submitter_id');
    }

    /**
     * Relasi ke notifikasi sebagai pengirim.
     */
    public function sentNotifications()
    {
        return $this->hasMany(Notification::class, 'sender_id');
    }
}