<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_hi',
        'name_en',
        'district',
        'latitude',
        'longitude',
        'is_major_junction',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_major_junction' => 'boolean',
    ];

    public function routeStops(): HasMany
    {
        return $this->hasMany(RouteStop::class);
    }

    public function originatingRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'source_stop_id');
    }

    public function terminatingRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'destination_stop_id');
    }
}

