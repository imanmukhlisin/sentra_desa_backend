<?php

namespace App\Models;

use App\Traits\HasUniqueSlug;
use App\Traits\TransformsImages;
use Illuminate\Database\Eloquent\Model;

class Bumdes extends Model
{
    use HasUniqueSlug, TransformsImages;

    protected array $imageFields   = ['logo'];
    protected array $galleryFields = ['gallery'];
    protected $table = 'bumdes';

    protected $fillable = [
        'village_id',
        'created_by',
        'name',
        'slug',
        'description',
        'logo',
        'gallery',
        'legal_number',
        'established_date',
        'initial_capital',
        'business_units',
        'director_name',
        'phone',
        'email',
        'address',
        'annual_revenue',
        'employee_count',
        'is_active',
        'performance_category',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'business_units' => 'array',
            'established_date' => 'date',
            'initial_capital' => 'decimal:2',
            'annual_revenue' => 'decimal:2',
            'is_active' => 'boolean',
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
