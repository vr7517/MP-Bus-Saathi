<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Services\RouteFinderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function __construct(protected RouteFinderService $routeFinder) {}

    /**
     * Search routes from source stop to destination stop.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'source_stop_id' => 'required|integer|exists:bus_stops,id',
            'destination_stop_id' => 'required|integer|exists:bus_stops,id|different:source_stop_id',
            'bus_type' => 'nullable|string|in:ordinary,express,ac,sleeper',
        ]);

        $results = $this->routeFinder->search(
            $validated['source_stop_id'],
            $validated['destination_stop_id'],
            $validated['bus_type'] ?? null
        );

        return response()->json([
            'status' => 'success',
            'data' => $results,
        ]);
    }

    /**
     * Get all active routes.
     */
    public function index(): JsonResponse
    {
        $routes = Route::with(['sourceStop', 'destinationStop'])
            ->where('is_active', true)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $routes,
        ]);
    }

    /**
     * Get ordered sequence of stops along a specific route.
     */
    public function stops(int $id): JsonResponse
    {
        $route = Route::with(['stops.busStop', 'sourceStop', 'destinationStop'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'route_id' => $route->id,
            'route_code' => $route->route_code,
            'source' => $route->sourceStop->name_hi . ' (' . $route->sourceStop->name_en . ')',
            'destination' => $route->destinationStop->name_hi . ' (' . $route->destinationStop->name_en . ')',
            'total_distance_km' => $route->total_distance_km,
            'estimated_duration_minutes' => $route->estimated_duration_minutes,
            'stops' => $route->stops->map(function ($rs) {
                return [
                    'stop_sequence' => $rs->stop_sequence,
                    'stop_id' => $rs->bus_stop_id,
                    'name_hi' => $rs->busStop->name_hi,
                    'name_en' => $rs->busStop->name_en,
                    'district' => $rs->busStop->district,
                    'latitude' => $rs->busStop->latitude,
                    'longitude' => $rs->busStop->longitude,
                    'distance_from_source_km' => $rs->distance_from_source_km,
                    'typical_travel_time_minutes' => $rs->typical_travel_time_minutes,
                ];
            }),
        ]);
    }
}

