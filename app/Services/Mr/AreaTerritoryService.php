<?php

namespace App\Services\Mr;

use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Singleton Service for Managing Medical Representative Territories & Areas.
 * 
 * Implements the Singleton pattern to provide a unified, cached, and consistent
 * operational interface for Country -> City -> Area hierarchy operations.
 */
class AreaTerritoryService
{
    private static ?self $instance = null;

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton.');
    }

    /**
     * Singleton instance accessor.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get active countries with cities and areas tree.
     */
    public function getActiveHierarchyTree(): Collection
    {
        return Cache::remember('mr_territory_hierarchy_tree', 3600, function () {
            return Country::where('is_active', true)
                ->with(['activeCities.activeAreas'])
                ->orderBy('sort_order')
                ->orderBy('name_en')
                ->get();
        });
    }

    /**
     * Get areas for a specific city.
     */
    public function getAreasByCity(int $cityId, bool $onlyActive = true): Collection
    {
        $cacheKey = "mr_areas_by_city_{$cityId}_" . ($onlyActive ? 'active' : 'all');

        return Cache::remember($cacheKey, 1800, function () use ($cityId, $onlyActive) {
            $query = Area::where('city_id', $cityId)->orderBy('sort_order')->orderBy('name_en');
            if ($onlyActive) {
                $query->where('is_active', true);
            }
            return $query->get();
        });
    }

    /**
     * Assign a Medical Representative to a specific Territory (Country, City, Area).
     */
    public function assignRepToTerritory(User $user, ?int $areaId, ?int $cityId = null, ?int $countryId = null): User
    {
        if ($areaId) {
            $area = Area::with('city.country')->find($areaId);
            if ($area) {
                $cityId = $area->city_id;
                $countryId = $area->country_id;
            }
        } elseif ($cityId && !$countryId) {
            $city = City::find($cityId);
            if ($city) {
                $countryId = $city->country_id;
            }
        }

        $user->update([
            'country_id' => $countryId,
            'city_id' => $cityId,
            'area_id' => $areaId,
        ]);

        Log::info("AreaTerritoryService: Assigned MR #{$user->id} ({$user->name}) to Country #{$countryId}, City #{$cityId}, Area #{$areaId}");

        $this->clearTerritoryCache();

        return $user->fresh(['country', 'city', 'area']);
    }

    /**
     * Get territory overview statistics for Admin dashboard.
     */
    public function getTerritoryStats(): array
    {
        return [
            'total_areas' => Area::count(),
            'active_areas' => Area::where('is_active', true)->count(),
            'covered_cities' => City::has('areas')->count(),
            'assigned_reps_count' => User::whereNotNull('area_id')->count(),
            'unassigned_reps_count' => User::where(function ($q) {
                $q->whereHas('role', function ($rq) {
                    $rq->where('name', 'mr');
                })->orWhere('role_id', 2);
            })->whereNull('area_id')->count(),
        ];
    }

    /**
     * Clear all territory cache keys.
     */
    public function clearTerritoryCache(): void
    {
        Cache::forget('mr_territory_hierarchy_tree');
    }
}
