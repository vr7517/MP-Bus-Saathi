<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'registration_number',
        'bus_type',
        'seating_capacity',
        'has_gps_device',
        'is_active',
    ];

    protected $casts = [
        'has_gps_device' => 'boolean',
        'is_active' => 'boolean',
        'seating_capacity' => 'integer',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function telemetry(): HasOne
    {
        return $this->hasOne(LiveBusTelemetry::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}

