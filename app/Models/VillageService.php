<?php

namespace App\Models;

use App\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Model;

class VillageService extends Model
{
    use HasUniqueSlug;
    protected $fillable = [
        'village_id',
        'name',
        'slug',
        'description',
        'icon',
        'category',
        'requirements',
        'process_steps',
        'estimated_days',
        'fee',
        'contact_person',
        'contact_phone',
        'office_hours',
        'is_online_available',
        'online_url',
        'is_active',
        'usage_count',
    ];

    protected function casts(): array
    {
        return [
            'requirements' => 'array',
            'is_online_available' => 'boolean',
            'is_active' => 'boolean',
            'fee' => 'decimal:2',
        ];
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }
}
