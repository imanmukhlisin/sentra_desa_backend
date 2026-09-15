<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = [
        'code',
        'name',
        'latitude',
        'longitude',
    ];

    public function regencies()
    {
        return $this->hasMany(Regency::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
