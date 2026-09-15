<?php

namespace App\Models;

use App\Traits\TransformsImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    use TransformsImages;

    protected array $imageFields = ['image'];
    protected $fillable = [
        'village_id',
        'created_by',
        'title',
        'description',
        'category',
        'quantity',
        'unit',
        'needed_by',
        'image',
        'status',
        'notified_count',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'needed_by' => 'date',
            'notified_at' => 'datetime',
        ];
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'open' => 'Terbuka',
            'fulfilled' => 'Terpenuhi',
            'closed' => 'Ditutup',
            default => ucfirst($this->status),
        };
    }

    public static function categoryOptions(): array
    {
        return [
            'pertanian' => 'Pertanian',
            'perkebunan' => 'Perkebunan',
            'perikanan' => 'Perikanan',
            'peternakan' => 'Peternakan',
            'industri' => 'Industri',
            'pariwisata' => 'Pariwisata',
            'tambang' => 'Tambang',
            'kehutanan' => 'Kehutanan',
            'sdm' => 'SDM',
            'kerajinan' => 'Kerajinan',
            'makanan_minuman' => 'Makanan & Minuman',
            'lainnya' => 'Lainnya',
        ];
    }
}
