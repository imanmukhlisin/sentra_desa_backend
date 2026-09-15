<?php

namespace App\Models;

use App\Traits\TransformsImages;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use TransformsImages;

    protected array $imageFields = ['logo', 'payment_proof', 'renewal_proof'];
    protected $fillable = [
        'user_id',
        'village_id',
        'store_name',
        'description',
        'logo',
        'phone',
        'address',
        'business_type',
        'entitas', // Tambahan entitas
        'established_year',
        'payment_proof',
        'status', // pending, approved, rejected
        'approved_at',
        'approved_by',
        'membership_expires_at',
        'renewal_proof',
        'renewal_requested_at',
        'renewal_status',
    ];

    protected $appends = ['is_membership_active', 'days_until_expiry'];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'membership_expires_at' => 'datetime',
            'renewal_requested_at' => 'datetime',
        ];
    }

    // ── Computed attributes ──

    public function getIsMembershipActiveAttribute(): bool
    {
        if ($this->status !== 'approved' || !$this->membership_expires_at) {
            return false;
        }
        return $this->membership_expires_at->isFuture();
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->membership_expires_at) return null;
        return (int) now()->diffInDays($this->membership_expires_at, false);
    }

    // ── Relationships ──

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}