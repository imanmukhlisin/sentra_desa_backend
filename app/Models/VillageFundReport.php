<?php

namespace App\Models;

use App\Traits\TransformsImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillageFundReport extends Model
{
    use TransformsImages;

    protected array $imageFields = ['infographic_image'];
    protected $fillable = [
        'village_id',
        'fiscal_year',
        'period',
        // Belanja
        'belanja_pemerintahan',
        'belanja_pembangunan',
        'belanja_pembinaan',
        'belanja_pemberdayaan',
        'belanja_bencana',
        // Pendapatan
        'pendapatan_asli_desa',
        'dana_desa',
        'bagi_hasil_pajak',
        'alokasi_dana_desa',
        'bantuan_keuangan_kab',
        'bantuan_keuangan_prov',
        'pendapatan_lainnya',
        // Pembiayaan
        'penerimaan_pembiayaan',
        'silpa',
        // Info
        'head_name',
        'head_title',
        'infographic_image',
        'is_published',
        'notes',
    ];

    protected $casts = [
        'fiscal_year' => 'integer',
        'belanja_pemerintahan' => 'decimal:2',
        'belanja_pembangunan' => 'decimal:2',
        'belanja_pembinaan' => 'decimal:2',
        'belanja_pemberdayaan' => 'decimal:2',
        'belanja_bencana' => 'decimal:2',
        'pendapatan_asli_desa' => 'decimal:2',
        'dana_desa' => 'decimal:2',
        'bagi_hasil_pajak' => 'decimal:2',
        'alokasi_dana_desa' => 'decimal:2',
        'bantuan_keuangan_kab' => 'decimal:2',
        'bantuan_keuangan_prov' => 'decimal:2',
        'pendapatan_lainnya' => 'decimal:2',
        'penerimaan_pembiayaan' => 'decimal:2',
        'silpa' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    // ── Relationships ──

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    // ── Computed Attributes ──

    public function getTotalBelanjaAttribute(): float
    {
        return (float) $this->belanja_pemerintahan
             + (float) $this->belanja_pembangunan
             + (float) $this->belanja_pembinaan
             + (float) $this->belanja_pemberdayaan
             + (float) $this->belanja_bencana;
    }

    public function getTotalPendapatanAttribute(): float
    {
        return (float) $this->pendapatan_asli_desa
             + (float) $this->dana_desa
             + (float) $this->bagi_hasil_pajak
             + (float) $this->alokasi_dana_desa
             + (float) $this->bantuan_keuangan_kab
             + (float) $this->bantuan_keuangan_prov
             + (float) $this->pendapatan_lainnya;
    }

    protected $appends = ['total_belanja', 'total_pendapatan'];
}
