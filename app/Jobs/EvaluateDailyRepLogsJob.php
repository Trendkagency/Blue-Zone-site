<?php

namespace App\Jobs;

use App\Models\Mr\ContactAssignment;
use App\Models\Mr\RepDailyLog;
use App\Models\Mr\VisitCycle;
use App\Models\User;
use App\Services\FcmService;
use App\Services\Mr\CrmMrReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EvaluateDailyRepLogsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(CrmMrReportService $reportService): void
    {
        $activeCycles = VisitCycle::where('status', 'active')->get();
        $today = now()->toDateString();

        foreach ($activeCycles as $cycle) {
            // Find all MRs assigned to this cycle
            $repIds = ContactAssignment::where('cycle_id', $cycle->id)
                ->distinct()
                ->pluck('mr_id');

            foreach ($repIds as $mrId) {
                $rep = User::find($mrId);
                if (!$rep) {
                    continue;
                }

                // Check or create daily log for today
                $log = RepDailyLog::where('mr_id', $mrId)
                    ->where('log_date', $today)
                    ->first();

                if (!$log) {
                    $log = RepDailyLog::create([
                        'mr_id' => $mrId,
                        'cycle_id' => $cycle->id,
                        'log_date' => $today,
                        'is_reported' => false,
                        'total_visits_count' => 0,
                    ]);
                }

                // Recalculate unreported days count for this rep
                $unreportedCount = $reportService->calculateUnreportedDaysForRep($mrId, $cycle);

                // If unreported days exceed threshold (>= 2 days), alert Line Managers & Admins
                if ($unreportedCount >= 2 && !$log->is_reported) {
                    $this->alertUnreportedThresholdExceeded($rep, $cycle, $unreportedCount);
                }
            }
        }
    }

    /**
     * Send FCM alert regarding high unreported days
     */
    protected function alertUnreportedThresholdExceeded(User $rep, VisitCycle $cycle, int $unreportedCount): void
    {
        try {
            $admins = User::whereHas('role', function ($q) {
                $q->whereIn('name', ['admin', 'super_admin', 'Super Admin', 'mr_line_manager']);
            })->get();

            $tokens = [];
            foreach ($admins as $admin) {
                if (method_exists($admin, 'getActiveFcmTokens')) {
                    $tokens = array_merge($tokens, $admin->getActiveFcmTokens());
                }
            }

            if (!empty($tokens)) {
                $fcm = FcmService::getInstance();
                $fcm->sendToTokens(
                    array_unique($tokens),
                    '⚠️ Rep Inactivity Alert',
                    "MR {$rep->name} has {$unreportedCount} unreported days in cycle '{$cycle->name}'.",
                    [
                        'type' => 'unreported_days_threshold',
                        'mr_id' => (string) $rep->id,
                        'cycle_id' => (string) $cycle->id,
                        'unreported_count' => (string) $unreportedCount,
                    ]
                );
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to dispatch unreported day FCM alert: " . $e->getMessage());
        }
    }
}
