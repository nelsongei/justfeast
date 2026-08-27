<?php

namespace App\Services;

class GeospatialService
{
    /**
     * Calculate spherical distance between two geographical points in meters using the Haversine formula.
     *
     * @param  float $lat1 Latitude of point 1 (in degrees)
     * @param  float $lng1 Longitude of point 1 (in degrees)
     * @param  float $lat2 Latitude of point 2 (in degrees)
     * @param  float $lng2 Longitude of point 2 (in degrees)
     * @return float Distance in meters rounded to 2 decimal places
     */
    public static function calculateHaversineDistanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusMeters = 6371000.0;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2.0) * sin($dLat / 2.0) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2.0) * sin($dLng / 2.0);

        $c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));

        return round($earthRadiusMeters * $c, 2);
    }
}
