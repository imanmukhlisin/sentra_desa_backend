<?php

namespace App\Models;

use App\Traits\TransformsImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use TransformsImages;

    protected array $imageFields   = ['image'];
    protected array $galleryFields = ['gallery'];
    protected $fillable = [
        'merchant_id',
        'village_id',
        'name',
        'slug',
        'price',
        'description',
        'image',
        'gallery',
        'category',
        'sku',
        'stock',
        'unit',
        'discount_price',
        'is_available',
        'weight',
        'tags',
        'view_count',
        'order_count',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'tags' => 'array',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $base = Str::slug($model->name);
                $slug = $base . '-' . Str::random(5);
                // Ensure uniqueness
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . Str::random(5);
                }
                $model->slug = $slug;
            }
        });
        static::updating(function ($model) {
            if ($model->isDirty('name')) {
                $base = Str::slug($model->name);
                $slug = $base . '-' . Str::random(5);
                while (static::where('slug', $slug)->where('id', '!=', $model->id)->exists()) {
                    $slug = $base . '-' . Str::random(5);
                }
                $model->slug = $slug;
            }
        });
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }
}