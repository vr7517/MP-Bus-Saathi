<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusStop;
use App\Models\CommunityVerification;
use App\Models\Schedule;
use App\Services\ETAService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommunityVerificationController extends Controller
{
    public function __construct(protected ETAService $etaService) {}

    /**
     * Submit passenger community verification for a bus schedule at a stop.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'schedule_id' => 'required|integer|exists:schedules,id',
            'bus_stop_id' => 'required|integer|exists:bus_stops,id',
            'device_hash' => 'required|string|max:64',
            'report_type' => 'required|string|in:on_time,delayed,cancelled,fare_update',
            'delay_minutes' => 'nullable|integer|min:0|max:300',
            'notes' => 'nullable|string|max:500',
            'user_latitude' => 'nullable|numeric|between:-90,90',
            'user_longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $stop = BusStop::findOrFail($validated['bus_stop_id']);
        $isWithinGeofence = true;

        // If user submitted coordinates, check if within 2.5 km of bus stop
        if (!empty($validated['user_latitude']) && !empty($validated['user_longitude'])) {
            $dist = $this->etaService->calculateDistanceKm(
                $validated['user_latitude'],
                $validated['user_longitude'],
                $stop->latitude,
                $stop->longitude
            );
            $isWithinGeofence = ($dist <= 2.5);
        }

        $verification = CommunityVerification::create([
            'schedule_id' => $validated['schedule_id'],
            'bus_stop_id' => $validated['bus_stop_id'],
            'device_hash' => hash('sha256', $validated['device_hash']),
            'report_type' => $validated['report_type'],
            'delay_minutes' => $validated['delay_minutes'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'verified_at' => Carbon::now(),
            'is_approved' => $isWithinGeofence, // Auto-approve if verified on-site
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'सत्यापन दर्ज किया गया। यात्रियों की सहायता के लिए धन्यवाद! (Verification recorded. Thank you!)',
            'data' => [
                'verification_id' => $verification->id,
                'verified_on_site' => $isWithinGeofence,
            ]
        ], 201);
    }

    /**
     * Get recent community verifications for a specific schedule.
     */
    public function index(int $scheduleId): JsonResponse
    {
        $reports = CommunityVerification::with('busStop')
            ->where('schedule_id', $scheduleId)
            ->where('verified_at', '>=', Carbon::now()->subHours(24))
            ->orderByDesc('verified_at')
            ->get();

        return response()->json([
            'status' => 'success',
            'count' => $reports->count(),
            'data' => $reports->map(function ($r) {
                return [
                    'id' => $r->id,
                    'stop_name' => $r->busStop->name_hi . ' (' . $r->busStop->name_en . ')',
                    'report_type' => $r->report_type,
                    'delay_minutes' => $r->delay_minutes,
                    'notes' => $r->notes,
                    'verified_at' => $r->verified_at->diffForHumans(),
                ];
            }),
        ]);
    }
}

