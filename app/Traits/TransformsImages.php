<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

/**
 * Transforms relative storage paths to full public URLs in API responses.
 *
 * Usage per model:
 *   protected array $imageFields   = ['image', 'cover_image'];   // single-value image columns
 *   protected array $galleryFields = ['gallery'];                 // JSON-array image columns
 *
 * Controllers still receive raw paths via $model->getRawOriginal('image'),
 * so file-deletion logic is unaffected.
 */
trait TransformsImages
{
    public function toArray(): array
    {
        $array = parent::toArray();

        foreach ($this->imageFields ?? [] as $field) {
            $path = $array[$field] ?? null;
            if ($path && !str_starts_with($path, 'http')) {
                $array[$field] = Storage::disk('public')->url($path);
            }
        }

        foreach ($this->galleryFields ?? [] as $field) {
            $items = $array[$field] ?? [];
            if (is_array($items)) {
                $array[$field] = collect($items)
                    ->filter()
                    ->map(fn ($p) => str_starts_with($p, 'http') ? $p : Storage::disk('public')->url($p))
                    ->values()
                    ->toArray();
            }
        }

        return $array;
    }
}
