<?php

namespace App\Models;

use App\Traits\TransformsImages;
use Illuminate\Database\Eloquent\Model;

class VillagePotential extends Model
{
    use TransformsImages;

    protected array $imageFields   = ['image'];
    protected array $galleryFields = ['gallery'];
    protected $fillable = [
        'village_id',
        'created_by',
        'category',
        'name',
        'description',
        'image',
        'gallery',
        'total_area',
        'production_volume',
        'economic_value',
        'is_investment_ready',
        'investment_needs',
        'development_status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'is_investment_ready' => 'boolean',
            'is_active' => 'boolean',
            'total_area' => 'decimal:2',
            'economic_value' => 'decimal:2',
        ];
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
