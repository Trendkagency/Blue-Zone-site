<?php

namespace App\Jobs;

use App\Models\Mr\VisitCycle;
use App\Services\Mr\CrmMrReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateRepPerformanceSnapshotsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(CrmMrReportService $reportService): void
    {
        $activeCycles = VisitCycle::where('status', 'active')->get();

        foreach ($activeCycles as $cycle) {
            $reportService->recalculateAllSnapshotsForCycle($cycle->id);
        }
    }
}
