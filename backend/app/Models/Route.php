<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_code',
        'source_stop_id',
        'destination_stop_id',
        'total_distance_km',
        'estimated_duration_minutes',
        'is_active',
    ];

    protected $casts = [
        'total_distance_km' => 'float',
        'estimated_duration_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    public function sourceStop(): BelongsTo
    {
        return $this->belongsTo(BusStop::class, 'source_stop_id');
    }

    public function destinationStop(): BelongsTo
    {
        return $this->belongsTo(BusStop::class, 'destination_stop_id');
    }

    public function stops(): HasMany
    {
        return $this->hasMany(RouteStop::class)->orderBy('stop_sequence');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}

