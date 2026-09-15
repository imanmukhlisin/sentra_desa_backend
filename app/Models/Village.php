<?php

namespace App\Models;

use App\Traits\TransformsImages;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use TransformsImages;

    protected array $imageFields   = ['logo', 'cover_image'];
    protected array $galleryFields = ['gallery'];
    protected $fillable = [
        'district_id',
        'code',
        'name',
        'postal_code',
        'description',
        'vision',
        'mission',
        'history',
        'head_name',
        'logo',
        'cover_image',
        'gallery',
        'website',
        'phone',
        'email',
        'latitude',
        'longitude',
        'population',
        'area_size',
        'dusun_count',
        'is_verified',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'area_size' => 'decimal:2',
        ];
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)
            ->orWhere('code', $value)
            ->firstOrFail();
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function merchants()
    {
        return $this->hasMany(Merchant::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function tourisms()
    {
        return $this->hasMany(Tourism::class);
    }

    public function bumdes()
    {
        return $this->hasMany(Bumdes::class);
    }

    public function villagePotentials()
    {
        return $this->hasMany(VillagePotential::class);
    }

    public function exportProducts()
    {
        return $this->hasMany(ExportProduct::class);
    }

    public function villageServices()
    {
        return $this->hasMany(VillageService::class);
    }

    public function villageContents()
    {
        return $this->hasMany(VillageContent::class);
    }

    public function villageFundReports()
    {
        return $this->hasMany(VillageFundReport::class);
    }

    public function kdmps()
    {
        // KDMP menyimpan village_ids sebagai JSON array
        return Kdmp::whereJsonContains('village_ids', $this->id)->get();
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
}
