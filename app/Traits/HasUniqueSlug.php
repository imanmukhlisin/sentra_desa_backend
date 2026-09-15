<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUniqueSlug
{
    protected static function bootHasUniqueSlug(): void
    {
        static::creating(function ($model) {
            $model->slug = static::buildUniqueSlug(
                $model->{static::slugSourceField()},
            );
        });

        static::updating(function ($model) {
            $source = static::slugSourceField();
            if ($model->isDirty($source)) {
                $model->slug = static::buildUniqueSlug(
                    $model->$source,
                    $model->id,
                );
            }
        });
    }

    protected static function slugSourceField(): string
    {
        return 'name';
    }

    protected static function buildUniqueSlug(string $value, ?int $excludeId = null): string
    {
        $base = Str::slug($value);
        $slug = $base;
        $i    = 1;

        while (
            static::where('slug', $slug)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
