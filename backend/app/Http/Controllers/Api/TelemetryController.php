<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\LiveBusTelemetry;
use App\Services\ETAService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TelemetryController extends Controller
{
    public function __construct(protected ETAService $etaService) {}

    /**
     * Ingest real-time GPS telemetry from vehicle device or driver app.
     */
    public function ping(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bus_id' => 'required|integer|exists:buses,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed_kmh' => 'nullable|numeric|min:0',
            'heading' => 'nullable|numeric|between:0,360',
            'active_schedule_id' => 'nullable|integer|exists:schedules,id',
        ]);

        $now = Carbon::now();

        $telemetry = LiveBusTelemetry::updateOrCreate(
            ['bus_id' => $validated['bus_id']],
            [
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'current_speed' => $validated['speed_kmh'] ?? 0.0,
                'heading' => $validated['heading'] ?? 0.0,
                'gps_status' => 'live',
                'last_ping_at' => $now,
                'active_schedule_id' => $validated['active_schedule_id'] ?? null,
            ]
        );

        // Update PostGIS Point geometry if on PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                "UPDATE live_bus_telemetry SET current_location = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE id = ?",
                [$validated['longitude'], $validated['latitude'], $telemetry->id]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Telemetry acknowledged',
            'last_ping_at' => $now->toIso8601String(),
        ]);
    }

    /**
     * Retrieve live tracking status and ETA for a bus.
     */
    public function status(int $busId): JsonResponse
    {
        $bus = Bus::with(['operator', 'telemetry.activeSchedule.route.stops.busStop'])->findOrFail($busId);
        $telemetry = $bus->telemetry;

        if (!$telemetry) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'bus_id' => $bus->id,
                    'registration_number' => $bus->registration_number,
                    'bus_type' => $bus->bus_type,
                    'operator' => $bus->operator?->name,
                    'gps_status' => 'unavailable',
                    'gps_status_label_hi' => 'जीपीएस अनुपलब्ध - निर्धारित समय-सारणी',
                    'gps_status_label_en' => 'GPS Unavailable - Scheduled Timetable',
                    'message' => 'No GPS hardware registered for this bus. Showing scheduled timetable.',
                    'last_ping_at' => null,
                    'current_location' => null,
                    'next_stop' => null,
                ]
            ]);
        }

        $isLive = $telemetry->isGpsLive();
        $secondsAgo = $telemetry->last_ping_at ? $telemetry->last_ping_at->diffInSeconds(Carbon::now()) : null;

        // Calculate next stop and ETA if on an active schedule
        $nextStopInfo = null;
        if ($isLive && $telemetry->activeSchedule?->route) {
            $routeStops = $telemetry->activeSchedule->route->stops;
            // Find closest upcoming stop along the path
            foreach ($routeStops as $rs) {
                $stop = $rs->busStop;
                $dist = $this->etaService->calculateDistanceKm(
                    $telemetry->latitude, $telemetry->longitude, $stop->latitude, $stop->longitude
                );

                if ($dist > 0.5) { // upcoming stop ahead
                    $etaMins = $this->etaService->computeEtaMinutes(
                        $telemetry->latitude, $telemetry->longitude, $stop->latitude, $stop->longitude, $telemetry->current_speed
                    );
                    $nextStopInfo = [
                        'stop_id' => $stop->id,
                        'name_hi' => $stop->name_hi,
                        'name_en' => $stop->name_en,
                        'district' => $stop->district,
                        'distance_km' => $dist,
                        'eta_minutes' => $etaMins,
                    ];
                    break;
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'bus_id' => $bus->id,
                'registration_number' => $bus->registration_number,
                'bus_type' => $bus->bus_type,
                'operator' => $bus->operator?->name,
                'gps_status' => $isLive ? 'live' : 'unavailable',
                'gps_status_label_hi' => $isLive ? 'लाइव जीपीएस ट्रैकिंग चालू' : 'जीपीएस अनुपलब्ध - निर्धारित समय-सारणी',
                'gps_status_label_en' => $isLive ? 'Live GPS Tracking Active' : 'GPS Unavailable - Scheduled Timetable',
                'current_location' => $isLive ? [
                    'latitude' => $telemetry->latitude,
                    'longitude' => $telemetry->longitude,
                    'speed_kmh' => $telemetry->current_speed,
                    'heading' => $telemetry->heading,
                ] : null,
                'last_ping_at' => $telemetry->last_ping_at?->toIso8601String(),
                'last_ping_seconds_ago' => $secondsAgo,
                'next_stop' => $nextStopInfo,
            ]
        ]);
    }
}
