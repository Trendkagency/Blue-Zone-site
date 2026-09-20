<?php

namespace App\Observers;

use App\Models\Mr\ContactAssignment;
use App\Models\Mr\RepDailyLog;
use App\Models\Mr\Visit;
use App\Models\User;
use App\Services\FcmService;
use App\Services\Mr\Strategies\ClassificationPointStrategy;
use Illuminate\Support\Facades\Log;

class VisitObserver
{
    protected ClassificationPointStrategy $pointStrategy;

    public function __construct()
    {
        $this->pointStrategy = new ClassificationPointStrategy();
    }

    /**
     * Handle the Visit "created" event.
     */
    public function created(Visit $visit): void
    {
        // 1. Maintain RepDailyLog immediately on check-in
        $logDate = $visit->checkin_at ? $visit->checkin_at->toDateString() : now()->toDateString();

        $dailyLog = RepDailyLog::where('mr_id', $visit->mr_id)
            ->whereDate('log_date', $logDate)
            ->first();

        if (!$dailyLog) {
            $dailyLog = RepDailyLog::create([
                'mr_id' => $visit->mr_id,
                'cycle_id' => $visit->cycle_id,
                'log_date' => $logDate,
                'is_reported' => true,
                'first_checkin_at' => $visit->checkin_at ?? now(),
                'total_visits_count' => 0,
            ]);
        }

        $dailyLog->increment('total_visits_count');
        if (!$dailyLog->is_reported) {
            $dailyLog->is_reported = true;
        }
        if (!$dailyLog->first_checkin_at) {
            $dailyLog->first_checkin_at = $visit->checkin_at;
        }
        $dailyLog->save();

        // 2. If GPS unverified, notify Admin / Line Manager via FCM
        if (!$visit->gps_verified) {
            $this->notifyAdminGpsAnomaly($visit);
        }
    }

    /**
     * Handle the Visit "updated" event.
     */
    public function updated(Visit $visit): void
    {
        // Update daily log with checkout time
        if ($visit->wasChanged('checkout_at') && $visit->checkout_at) {
            $logDate = $visit->checkout_at->toDateString();
            $dailyLog = RepDailyLog::where('mr_id', $visit->mr_id)
                ->where('log_date', $logDate)
                ->first();

            if ($dailyLog) {
                $dailyLog->update([
                    'last_checkout_at' => $visit->checkout_at,
                    'is_reported' => true,
                ]);
            }
        }

        // Recalculate ContactAssignment progress when checkout is recorded or updated
        if ($visit->assignment_id && ($visit->wasChanged('checkout_at') || $visit->wasChanged('outcome'))) {
            $assignment = ContactAssignment::with('contact.classification')->find($visit->assignment_id);
            if ($assignment) {
                $completedVisits = Visit::where('assignment_id', $assignment->id)
                    ->whereNotNull('checkout_at')
                    ->count();

                $achievedPoints = $this->pointStrategy->calculateAchievedPoints($assignment, $completedVisits);

                $assignment->update([
                    'visits_done' => $completedVisits,
                    'achieved_points' => $achievedPoints,
                ]);
            }
        }
    }

    /**
     * Send FCM Push Alert to admins on GPS anomalies
     */
    protected function notifyAdminGpsAnomaly(Visit $visit): void
    {
        try {
            $rep = User::find($visit->mr_id);
            $doctorName = $visit->contact?->name ?? 'Doctor';
            $repName = $rep?->name ?? 'Representative';
            $flag = $visit->gps_flag ?? 'unverified';

            $message = match ($flag) {
                'distance_exceeded' => "Rep {$repName} checked in {$visit->distance_from_contact_m}m away from {$doctorName} (outside allowed radius).",
                'gps_disabled' => "Rep {$repName} checked in without GPS coordinates at {$doctorName}.",
                'mock_suspected' => "Rep {$repName} check-in flagged for suspicious/mock location at {$doctorName}.",
                default => "Unverified visit location recorded for {$repName} at {$doctorName}.",
            };

            // Find Super Admins
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
                    '⚠️ GPS Check-In Anomaly Alert',
                    $message,
                    [
                        'type' => 'gps_anomaly',
                        'visit_id' => (string) $visit->id,
                        'mr_id' => (string) $visit->mr_id,
                    ]
                );
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to dispatch GPS anomaly FCM alert: " . $e->getMessage());
        }
    }
}
