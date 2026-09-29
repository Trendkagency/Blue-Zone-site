<?php

namespace App\Observers;

use App\Models\AreaBreak;
use App\Services\Mr\AreaTerritoryService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Observer for AreaBreak Model.
 * 
 * Implements the Observer design pattern to monitor AreaBreak lifecycle
 * events, automatically evict relevant cache keys, and audit territory break changes.
 */
class AreaBreakObserver
{
    public function created(AreaBreak $break): void
    {
        $this->evictCaches($break);
        Log::info("AreaBreakObserver: New Break created #{$break->id} [{$break->name_en} / {$break->name_ar}] under Area #{$break->area_id}.");
    }

    public function updated(AreaBreak $break): void
    {
        $this->evictCaches($break);
        Log::info("AreaBreakObserver: Break updated #{$break->id} [{$break->name_en}]. Status: " . ($break->is_active ? 'Active' : 'Inactive'));
    }

    public function deleted(AreaBreak $break): void
    {
        $this->evictCaches($break);
        Log::warning("AreaBreakObserver: Break deleted #{$break->id} [{$break->name_en}].");
    }

    public function restored(AreaBreak $break): void
    {
        $this->evictCaches($break);
        Log::info("AreaBreakObserver: Break restored #{$break->id} [{$break->name_en}].");
    }

    private function evictCaches(AreaBreak $break): void
    {
        Cache::forget("mr_breaks_by_area_{$break->area_id}_active");
        Cache::forget("mr_breaks_by_area_{$break->area_id}_all");
        AreaTerritoryService::getInstance()->clearTerritoryCache();
    }
}
