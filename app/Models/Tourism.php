<?php

namespace App\Models;

use App\Traits\HasUniqueSlug;
use App\Traits\TransformsImages;
use Illuminate\Database\Eloquent\Model;

class Tourism extends Model
{
    use HasUniqueSlug, TransformsImages;

    protected array $imageFields   = ['cover_image'];
    protected array $galleryFields = ['gallery'];
    protected $fillable = [
        'village_id',
        'created_by',
        'name',
        'slug',
        'description',
        'short_description',
        'cover_image',
        'gallery',
        'category',
        'entrance_fee',
        'opening_hours',
        'facilities',
        'address',
        'latitude',
        'longitude',
        'phone',
        'website',
        'is_active',
        'is_featured',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'entrance_fee' => 'decimal:2',
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
