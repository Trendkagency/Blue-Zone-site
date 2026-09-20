<?php

namespace App\Services\Mr;

use App\Models\Mr\GpsConfig;
use App\Models\User;

class GpsValidationService
{
    private static ?GpsValidationService $instance = null;

    /**
     * Singleton instance accessor
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Calculate geodesic distance between two points in meters using Haversine formula
     */
    public function calculateDistanceInMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Earth radius in meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Validate GPS coordinates against contact clinic location and rep configuration
     *
     * @param float|null $checkinLat Check-in latitude
     * @param float|null $checkinLng Check-in longitude
     * @param float|null $contactLat Contact registered latitude
     * @param float|null $contactLng Contact registered longitude
     * @param User|int|null $user The representative
     * @param array $deviceMeta Device metadata / mock indicators
     * @return array ['verified' => bool, 'distance_m' => float|null, 'flag' => string]
     */
    public function validateCheckIn(
        ?float $checkinLat,
        ?float $checkinLng,
        ?float $contactLat,
        ?float $contactLng,
        User|int|null $user = null,
        array $deviceMeta = []
    ): array {
        $userId = $user instanceof User ? $user->id : $user;
        $config = $this->getEffectiveConfig($userId);

        // 1. If GPS is missing or 0,0
        if ($checkinLat === null || $checkinLng === null || ($checkinLat == 0 && $checkinLng == 0)) {
            return [
                'verified' => false,
                'distance_m' => null,
                'flag' => 'gps_disabled',
            ];
        }

        // 2. Check for mock location indicator in device metadata
        if ($config->mock_detection_enabled) {
            if (!empty($deviceMeta['is_mock']) || !empty($deviceMeta['mock_location'])) {
                return [
                    'verified' => false,
                    'distance_m' => null,
                    'flag' => 'mock_suspected',
                ];
            }
        }

        // 3. If contact has no registered coordinates, verify if GPS required
        if ($contactLat === null || $contactLng === null || ($contactLat == 0 && $contactLng == 0)) {
            return [
                'verified' => true, // Contact coords not set yet, cannot enforce distance
                'distance_m' => null,
                'flag' => 'verified',
            ];
        }

        // 4. Calculate Haversine distance
        $distance = $this->calculateDistanceInMeters($checkinLat, $checkinLng, $contactLat, $contactLng);
        $allowedRadius = $config->allowed_radius_m ?? 150;

        if ($distance <= $allowedRadius) {
            return [
                'verified' => true,
                'distance_m' => $distance,
                'flag' => 'verified',
            ];
        }

        return [
            'verified' => false,
            'distance_m' => $distance,
            'flag' => 'distance_exceeded',
        ];
    }

    /**
     * Get effective GPS configuration for a user or system default
     */
    public function getEffectiveConfig(?int $userId = null): GpsConfig
    {
        if ($userId) {
            $userConfig = GpsConfig::where('user_id', $userId)->first();
            if ($userConfig) {
                return $userConfig;
            }
        }

        $defaultConfig = GpsConfig::whereNull('user_id')->first();
        if ($defaultConfig) {
            return $defaultConfig;
        }

        // In-memory fallback
        $fallback = new GpsConfig();
        $fallback->allowed_radius_m = 150;
        $fallback->is_gps_required = true;
        $fallback->mock_detection_enabled = true;

        return $fallback;
    }
}
