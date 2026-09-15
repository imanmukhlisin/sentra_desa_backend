<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regency extends Model
{
    protected $fillable = [
        'province_id',
        'code',
        'name',
        'type',
        'latitude',
        'longitude',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function districts()
    {
        return $this->hasMany(District::class);
    }

    public function kdmps()
    {
        return $this->hasMany(Kdmp::class);
    }
}
