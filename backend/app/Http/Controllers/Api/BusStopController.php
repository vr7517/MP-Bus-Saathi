<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusStop;
use App\Services\ETAService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusStopController extends Controller
{
    public function __construct(protected ETAService $etaService) {}

    /**
     * Search bus stops by Hindi/English name, district, or proximity.
     */
    public function index(Request $request): JsonResponse
    {
        $query = BusStop::query();

        if ($search = $request->input('query')) {
            $likeOperator = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
            $query->where(function ($q) use ($search, $likeOperator) {
                $q->where('name_en', $likeOperator, "%{$search}%")
                  ->orWhere('name_hi', 'LIKE', "%{$search}%")
                  ->orWhere('district', $likeOperator, "%{$search}%");
            });
        }

        if ($district = $request->input('district')) {
            $query->where('district', $district);
        }

        $stops = $query->orderBy('name_en')->get();

        // Calculate proximity if coordinates provided
        if ($request->filled(['lat', 'lng'])) {
            $userLat = (float) $request->input('lat');
            $userLng = (float) $request->input('lng');

            $stops = $stops->map(function ($stop) use ($userLat, $userLng) {
                $stop->distance_km = $this->etaService->calculateDistanceKm(
                    $userLat, $userLng, $stop->latitude, $stop->longitude
                );
                return $stop;
            })->sortBy('distance_km')->values();

            if ($radius = $request->input('radius_km')) {
                $stops = $stops->where('distance_km', '<=', (float) $radius)->values();
            }
        }

        return response()->json([
            'status' => 'success',
            'count' => $stops->count(),
            'data' => $stops,
        ]);
    }

    /**
     * Get details of a single bus stop.
     */
    public function show(int $id): JsonResponse
    {
        $stop = BusStop::with([
            'routeStops.route.destinationStop',
            'routeStops.route.sourceStop',
        ])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $stop,
        ]);
    }
}
