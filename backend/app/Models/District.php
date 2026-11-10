<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_hi',
        'code',
        'division',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
    ];

    public function busStops(): HasMany
    {
        return $this->hasMany(BusStop::class, 'district', 'name_en');
    }

    public function displayName(bool $isHindi = true): string
    {
        return $isHindi ? $this->name_hi : $this->name_en;
    }
}
