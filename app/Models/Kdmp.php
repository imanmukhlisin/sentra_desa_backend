<?php

namespace App\Models;

use App\Traits\TransformsImages;
use Illuminate\Database\Eloquent\Model;

class Kdmp extends Model
{
    use TransformsImages;

    protected array $imageFields   = ['cover_image'];
    protected array $galleryFields = ['gallery'];
    protected $table = 'kdmp';

    protected $fillable = [
        'village_id',
        'name',
        'code',
        'nomor_badan_hukum',
        'description',
        'cover_image',
        'gallery',
        'status',
        'unit_usaha',
        'ketua_name',
        'sekretaris_name',
        'bendahara_name',
        'total_members',
        'modal_awal',
        'total_assets',
        'established_date',
        'address',
        'phone',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'gallery'         => 'array',
            'unit_usaha'      => 'array',
            'is_active'       => 'boolean',
            'modal_awal'      => 'decimal:2',
            'total_assets'    => 'decimal:2',
            'established_date'=> 'date',
        ];
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }

    public static function unitUsahaOptions(): array
    {
        return [
            'simpan_pinjam' => 'Simpan Pinjam',
            'perdagangan'   => 'Perdagangan',
            'pertanian'     => 'Pertanian',
            'peternakan'    => 'Peternakan',
            'perikanan'     => 'Perikanan',
            'jasa'          => 'Jasa Umum',
            'pariwisata'    => 'Pariwisata',
        ];
    }
}
