<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'bus_stop_id',
        'stop_sequence',
        'distance_from_source_km',
        'typical_travel_time_minutes',
    ];

    protected $casts = [
        'stop_sequence' => 'integer',
        'distance_from_source_km' => 'float',
        'typical_travel_time_minutes' => 'integer',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function busStop(): BelongsTo
    {
        return $this->belongsTo(BusStop::class);
    }
}

