<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'village_id',
        'regency_id',
        'province_id',
        'user_level',
        'phone',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $guard_name = 'web';

    /**
     * Filament 3: tentukan siapa yang boleh akses panel admin.
     * Hanya superadmin, province_admin, regency_admin, village_admin yang bisa masuk.
     * UMKM dan Buyer tidak diizinkan akses admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->user_level, [
            'superadmin',
            'province_admin',
            'regency_admin',
            'village_admin',
        ]);
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}