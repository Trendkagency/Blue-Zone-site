<?php

namespace App\Observers;

use App\Models\Area;
use App\Services\Mr\AreaTerritoryService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Observer for Area Model.
 * 
 * Implements the Observer design pattern to monitor Area lifecycle
 * events, automatically evict relevant cache tags, and audit territory changes.
 */
class AreaObserver
{
    public function created(Area $area): void
    {
        $this->evictCaches($area);
        Log::info("AreaObserver: New Area created #{$area->id} [{$area->name_en} / {$area->name_ar}] under City #{$area->city_id}.");
    }

    public function updated(Area $area): void
    {
        $this->evictCaches($area);
        Log::info("AreaObserver: Area updated #{$area->id} [{$area->name_en}]. Status: " . ($area->is_active ? 'Active' : 'Inactive'));
    }

    public function deleted(Area $area): void
    {
        $this->evictCaches($area);
        Log::warning("AreaObserver: Area deleted #{$area->id} [{$area->name_en}].");
    }

    public function restored(Area $area): void
    {
        $this->evictCaches($area);
        Log::info("AreaObserver: Area restored #{$area->id} [{$area->name_en}].");
    }

    private function evictCaches(Area $area): void
    {
        Cache::forget("mr_areas_by_city_{$area->city_id}_active");
        Cache::forget("mr_areas_by_city_{$area->city_id}_all");
        AreaTerritoryService::getInstance()->clearTerritoryCache();
    }
}
