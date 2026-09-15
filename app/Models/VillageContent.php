<?php

namespace App\Models;

use App\Traits\HasUniqueSlug;
use App\Traits\TransformsImages;
use Illuminate\Database\Eloquent\Model;

class VillageContent extends Model
{
    use HasUniqueSlug, TransformsImages;

    protected array $imageFields   = ['image'];
    protected array $galleryFields = ['gallery'];
    protected $fillable = [
        'village_id',
        'title',
        'slug',
        'category',
        'content',
        'image',
        'gallery',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
        ];
    }

    protected static function slugSourceField(): string
    {
        return 'title';
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }
}