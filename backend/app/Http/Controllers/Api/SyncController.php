<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\BusStop;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    /**
     * Download or incrementally sync the complete static transit dataset
     * to power 100% offline searches and timetables on rural Android devices.
     */
    public function snapshot(Request $request): JsonResponse
    {
        $since = $request->input('since');
        $sinceDate = $since ? Carbon::parse($since) : null;

        $stopsQuery = BusStop::query();
        $routesQuery = Route::where('is_active', true);
        $routeStopsQuery = RouteStop::query();
        $schedulesQuery = Schedule::where('is_active', true);
        $busesQuery = Bus::where('is_active', true)->with('operator');

        if ($sinceDate) {
            $stopsQuery->where('updated_at', '>=', $sinceDate);
            $routesQuery->where('updated_at', '>=', $sinceDate);
            $routeStopsQuery->where('updated_at', '>=', $sinceDate);
            $schedulesQuery->where('updated_at', '>=', $sinceDate);
            $busesQuery->where('updated_at', '>=', $sinceDate);
        }

        return response()->json([
            'status' => 'success',
            'synced_at' => Carbon::now()->toIso8601String(),
            'is_incremental' => (bool) $sinceDate,
            'data' => [
                'stops' => $stopsQuery->get(),
                'routes' => $routesQuery->get(),
                'route_stops' => $routeStopsQuery->get(),
                'schedules' => $schedulesQuery->get(),
                'buses' => $busesQuery->get()->map(function ($b) {
                    return [
                        'id' => $b->id,
                        'registration_number' => $b->registration_number,
                        'bus_type' => $b->bus_type,
                        'operator_name' => $b->operator?->name,
                        'has_gps' => $b->has_gps_device,
                    ];
                }),
            ]
        ]);
    }
}

