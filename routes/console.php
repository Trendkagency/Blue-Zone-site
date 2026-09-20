<?php

use App\Jobs\CalculateRepPerformanceSnapshotsJob;
use App\Jobs\EvaluateDailyRepLogsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule MR Daily Inactivity and Unreported Day Evaluation at 23:00 daily
Schedule::job(new EvaluateDailyRepLogsJob())->dailyAt('23:00');

// Schedule Nightly Recalculation of Rep Performance Snapshots at 00:30
Schedule::job(new CalculateRepPerformanceSnapshotsJob())->dailyAt('00:30');

// Artisan Command to trigger recalculation manually
Artisan::command('mr:recalculate-kpis {cycle_id?}', function (?int $cycle_id = null) {
    $service = app(\App\Services\Mr\CrmMrReportService::class);
    if ($cycle_id) {
        $service->recalculateAllSnapshotsForCycle($cycle_id);
        $this->info("Recalculated MR KPIs for cycle {$cycle_id}.");
    } else {
        $activeCycles = \App\Models\Mr\VisitCycle::where('status', 'active')->get();
        foreach ($activeCycles as $cycle) {
            $service->recalculateAllSnapshotsForCycle($cycle->id);
            $this->info("Recalculated MR KPIs for active cycle {$cycle->name}.");
        }
    }
})->purpose('Recalculate Medical Representative Performance Snapshots and KPIs');

