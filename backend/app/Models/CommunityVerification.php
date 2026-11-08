<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'bus_stop_id',
        'device_hash',
        'report_type',
        'delay_minutes',
        'notes',
        'verified_at',
        'is_approved',
    ];

    protected $casts = [
        'delay_minutes' => 'integer',
        'verified_at' => 'datetime',
        'is_approved' => 'boolean',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function busStop(): BelongsTo
    {
        return $this->belongsTo(BusStop::class);
    }
}

