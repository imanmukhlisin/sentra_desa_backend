<?php

namespace App\Models;

use App\Traits\TransformsImages;
use Illuminate\Database\Eloquent\Model;

class Highlight extends Model
{
    use TransformsImages;

    protected array $imageFields = ['image'];
    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'link_url',
        'link_label',
        'sort_order',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    /**
     * Scope: only active highlights within date range
     */
    public function scopePublished($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }
}
