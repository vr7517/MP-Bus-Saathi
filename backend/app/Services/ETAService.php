<?php

namespace App\Services;

class ETAService
{
    /**
     * Calculate Haversine distance between two coordinates in kilometers.
     */
    public function calculateDistanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Compute estimated arrival time in minutes.
     * Incorporates road curvature factor (1.25x straight-line distance) and adaptive speed.
     */
    public function computeEtaMinutes(float $currentLat, float $currentLng, float $targetLat, float $targetLng, float $currentSpeedKmh): int
    {
        $straightDist = $this->calculateDistanceKm($currentLat, $currentLng, $targetLat, $targetLng);
        $roadDist = $straightDist * 1.25; // Highway curvature factor

        // Average transit speed fallback if bus is stopped at a traffic signal/stand
        $effectiveSpeed = max($currentSpeedKmh, 35.0);

        $hours = $roadDist / $effectiveSpeed;
        return max(1, (int) round($hours * 60));
    }
}

