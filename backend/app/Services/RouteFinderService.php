<?php

namespace App\Services;

use App\Models\BusStop;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RouteFinderService
{
    /**
     * Find direct and 1-transfer transit routes between two stops.
     */
    public function search(int $sourceStopId, int $destinationStopId, ?string $busType = null): array
    {
        $directRoutes = $this->findDirectRoutes($sourceStopId, $destinationStopId, $busType);
        $transferRoutes = [];

        // If direct routes are few or none, search 1-transfer connecting routes
        if ($directRoutes->count() < 3) {
            $transferRoutes = $this->findTransferRoutes($sourceStopId, $destinationStopId, $busType);
        }

        return [
            'source_stop' => BusStop::find($sourceStopId),
            'destination_stop' => BusStop::find($destinationStopId),
            'direct_routes' => $directRoutes,
            'transfer_routes' => $transferRoutes,
        ];
    }

    /**
     * Locate direct routes where source stop precedes destination stop.
     */
    protected function findDirectRoutes(int $sourceId, int $destId, ?string $busType = null): Collection
    {
        // Find matching route IDs and stop sequences
        $matches = DB::table('route_stops as rs1')
            ->join('route_stops as rs2', 'rs1.route_id', '=', 'rs2.route_id')
            ->join('routes as r', 'rs1.route_id', '=', 'r.id')
            ->where('rs1.bus_stop_id', $sourceId)
            ->where('rs2.bus_stop_id', $destId)
            ->whereColumn('rs1.stop_sequence', '<', 'rs2.stop_sequence')
            ->where('r.is_active', true)
            ->select([
                'r.id as route_id',
                'r.route_code',
                'rs1.stop_sequence as source_seq',
                'rs2.stop_sequence as dest_seq',
                'rs1.distance_from_source_km as source_dist',
                'rs2.distance_from_source_km as dest_dist',
                'rs1.typical_travel_time_minutes as source_time',
                'rs2.typical_travel_time_minutes as dest_time',
            ])
            ->get();

        $results = collect();

        foreach ($matches as $match) {
            $route = Route::with(['sourceStop', 'destinationStop'])->find($match->route_id);
            if (!$route) continue;

            $distanceKm = max(0, round($match->dest_dist - $match->source_dist, 2));
            $durationMins = max(0, $match->dest_time - $match->source_time);

            $schedulesQuery = Schedule::with(['bus.operator', 'bus.telemetry'])
                ->where('route_id', $route->id)
                ->where('is_active', true);

            if ($busType) {
                $schedulesQuery->whereHas('bus', function ($q) use ($busType) {
                    $q->where('bus_type', $busType);
                });
            }

            $schedules = $schedulesQuery->orderBy('departure_time')->get()->map(function ($sched) use ($match, $durationMins) {
                $bus = $sched->bus;
                $telemetry = $bus?->telemetry;
                $isLive = $telemetry?->isGpsLive() ?? false;

                // Estimated departure from this source stop
                $scheduledDep = Carbon::parse($sched->departure_time)->addMinutes($match->source_time);
                $scheduledArr = (clone $scheduledDep)->addMinutes($durationMins);

                return [
                    'schedule_id' => $sched->id,
                    'bus_id' => $bus?->id,
                    'bus_number' => $bus?->registration_number ?? 'Not Assigned',
                    'operator_name' => $bus?->operator?->name ?? 'State Transit',
                    'bus_type' => $bus?->bus_type ?? 'ordinary',
                    'has_gps' => $bus?->has_gps_device ?? false,
                    'gps_status' => $isLive ? 'live' : 'unavailable',
                    'departure_time' => $scheduledDep->format('H:i:s'),
                    'arrival_time' => $scheduledArr->format('H:i:s'),
                    'telemetry' => $isLive ? [
                        'latitude' => $telemetry->latitude,
                        'longitude' => $telemetry->longitude,
                        'speed_kmh' => $telemetry->current_speed,
                        'last_ping_seconds_ago' => $telemetry->last_ping_at->diffInSeconds(Carbon::now()),
                    ] : null,
                ];
            });

            $results->push([
                'route_id' => $route->id,
                'route_code' => $route->route_code,
                'origin_stop' => $route->sourceStop->name_hi . ' (' . $route->sourceStop->name_en . ')',
                'final_destination_stop' => $route->destinationStop->name_hi . ' (' . $route->destinationStop->name_en . ')',
                'travel_distance_km' => $distanceKm,
                'estimated_duration_minutes' => $durationMins,
                'stops_in_segment' => $match->dest_seq - $match->source_seq + 1,
                'schedules' => $schedules,
            ]);
        }

        return $results;
    }

    /**
     * Find 1-transfer routes through major interchange hubs.
     */
    protected function findTransferRoutes(int $sourceId, int $destId, ?string $busType = null): Collection
    {
        // Major junction interchange stops
        $junctions = BusStop::where('is_major_junction', true)
            ->whereNotIn('id', [$sourceId, $destId])
            ->get();

        $transfers = collect();

        foreach ($junctions as $junction) {
            $leg1Routes = $this->findDirectRoutes($sourceId, $junction->id, $busType);
            if ($leg1Routes->isEmpty()) continue;

            $leg2Routes = $this->findDirectRoutes($junction->id, $destId, $busType);
            if ($leg2Routes->isEmpty()) continue;

            $transfers->push([
                'interchange_stop' => [
                    'id' => $junction->id,
                    'name_hi' => $junction->name_hi,
                    'name_en' => $junction->name_en,
                    'district' => $junction->district,
                ],
                'leg_1' => $leg1Routes->first(),
                'leg_2' => $leg2Routes->first(),
                'total_distance_km' => round($leg1Routes->first()['travel_distance_km'] + $leg2Routes->first()['travel_distance_km'], 2),
                'total_estimated_duration_minutes' => $leg1Routes->first()['estimated_duration_minutes'] + $leg2Routes->first()['estimated_duration_minutes'] + 30, // 30 mins layover
            ]);
        }

        return $transfers->sortBy('total_estimated_duration_minutes')->values();
    }
}

