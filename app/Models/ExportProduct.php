<?php

namespace App\Models;

use App\Traits\HasUniqueSlug;
use App\Traits\TransformsImages;
use Illuminate\Database\Eloquent\Model;

class ExportProduct extends Model
{
    use HasUniqueSlug, TransformsImages;

    protected array $imageFields   = ['image'];
    protected array $galleryFields = ['gallery'];
    protected $fillable = [
        'village_id',
        'product_id',
        'created_by',
        'name',
        'slug',
        'description',
        'image',
        'gallery',
        'hs_code',
        'destination_countries',
        'export_status',
        'certifications',
        'has_export_license',
        'export_volume',
        'export_value',
        'unit',
        'contact_person',
        'contact_phone',
        'contact_email',
        'is_active',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'destination_countries' => 'array',
            'certifications' => 'array',
            'has_export_license' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'export_value' => 'decimal:2',
        ];
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
