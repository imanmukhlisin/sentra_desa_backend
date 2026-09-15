<?php

namespace App\Models;

use App\Traits\HasUniqueSlug;
use App\Traits\TransformsImages;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasUniqueSlug, TransformsImages;

    protected array $imageFields   = ['thumbnail'];
    protected array $galleryFields = ['gallery'];

    protected $fillable = [
        'village_id',
        'created_by',
        'title',
        'slug',
        'excerpt',
        'content',
        'thumbnail',
        'gallery',
        'category',
        'author_name',
        'status',
        'is_featured',
        'published_at',
        'read_time',
        'view_count',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'gallery'      => 'array',
            'tags'         => 'array',
            'is_featured'  => 'boolean',
            'published_at' => 'datetime',
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
