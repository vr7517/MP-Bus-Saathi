<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveBusTelemetry extends Model
{
    use HasFactory;

    protected $table = 'live_bus_telemetry';

    protected $fillable = [
        'bus_id',
        'latitude',
        'longitude',
        'current_speed',
        'heading',
        'gps_status',
        'last_ping_at',
        'active_schedule_id',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'current_speed' => 'float',
        'heading' => 'float',
        'last_ping_at' => 'datetime',
    ];

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function activeSchedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'active_schedule_id');
    }

    /**
     * Check if the telemetry ping is fresh (<= 180 seconds)
     */
    public function isGpsLive(): bool
    {
        if (!$this->last_ping_at) {
            return false;
        }

        return $this->last_ping_at->diffInSeconds(Carbon::now()) <= 180;
    }

    /**
     * Get computed GPS health status
     */
    public function getEffectiveGpsStatusAttribute(): string
    {
        return $this->isGpsLive() ? 'live' : 'unavailable';
    }
}

