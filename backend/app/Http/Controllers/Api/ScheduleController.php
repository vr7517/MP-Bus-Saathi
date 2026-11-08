<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;

class ScheduleController extends Controller
{
    /**
     * Get timetables for a specific route.
     */
    public function byRoute(int $routeId): JsonResponse
    {
        $schedules = Schedule::with(['bus.operator', 'bus.telemetry'])
            ->where('route_id', $routeId)
            ->where('is_active', true)
            ->orderBy('departure_time')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $schedules->map(function ($s) {
                $isLive = $s->bus?->telemetry?->isGpsLive() ?? false;
                return [
                    'schedule_id' => $s->id,
                    'departure_time' => $s->departure_time,
                    'expected_arrival_time' => $s->expected_arrival_time,
                    'days_of_week' => $s->days_of_week,
                    'bus' => $s->bus ? [
                        'id' => $s->bus->id,
                        'registration_number' => $s->bus->registration_number,
                        'bus_type' => $s->bus->bus_type,
                        'seating_capacity' => $s->bus->seating_capacity,
                        'operator' => $s->bus->operator?->name,
                        'has_gps' => $s->bus->has_gps_device,
                        'gps_status' => $isLive ? 'live' : 'unavailable',
                    ] : null,
                ];
            }),
        ]);
    }
}

