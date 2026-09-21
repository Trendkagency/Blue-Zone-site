<?php

namespace App\Services\Mr;

use App\Models\Mr\Contact;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\VisitCycle;
use App\Models\User;
use App\Services\FcmService;
use App\Services\Mr\Strategies\ClassificationPointStrategy;
use App\Services\Mr\Strategies\PointStrategyInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CrmAssignmentService
{
    protected PointStrategyInterface $pointStrategy;

    public function __construct(?PointStrategyInterface $pointStrategy = null)
    {
        $this->pointStrategy = $pointStrategy ?? new ClassificationPointStrategy();
    }

    /**
     * Assign a single doctor contact to a Medical Representative for a specific cycle
     */
    public function assignContact(int $cycleId, int $mrId, int $contactId): ContactAssignment
    {
        $contact = Contact::with('classification')->findOrFail($contactId);
        $class = $contact->classification;

        $targetVisits = $class ? (int) $class->required_visits : 1;
        $targetPoints = $this->pointStrategy->calculateTargetPoints($contact, $class);

        return ContactAssignment::updateOrCreate(
            [
                'cycle_id' => $cycleId,
                'mr_id' => $mrId,
                'contact_id' => $contactId,
            ],
            [
                'target_visits' => $targetVisits,
                'target_points' => $targetPoints,
                'is_active' => true,
            ]
        );
    }

    /**
     * Bulk assign contacts to an MR for a cycle, with optional automated visit scheduling.
     *
     * @param int $cycleId
     * @param int $mrId
     * @param array<int> $contactIds
     * @param bool $autoSchedule
     * @param array $scheduleOptions
     * @return array [assigned_count, scheduled_count]
     */
    public function bulkAssignContacts(
        int $cycleId,
        int $mrId,
        array $contactIds,
        bool $autoSchedule = false,
        array $scheduleOptions = []
    ): array {
        $count = 0;
        $assignments = [];

        DB::transaction(function () use ($cycleId, $mrId, $contactIds, &$count, &$assignments) {
            foreach ($contactIds as $contactId) {
                $assignment = $this->assignContact($cycleId, $mrId, (int) $contactId);
                $assignments[] = $assignment;
                $count++;
            }
        });

        // Automated Visit Scheduling if requested
        $scheduledVisitsCount = 0;
        if ($autoSchedule && !empty($assignments)) {
            $scheduleService = app(CrmScheduleService::class);
            $scheduledVisitsCount = $scheduleService->generateAutoScheduleForAssignments($assignments, $scheduleOptions);
        }

        // Trigger FCM push notification to MR
        try {
            $user = User::find($mrId);
            if ($user && method_exists($user, 'getActiveFcmTokens')) {
                $tokens = $user->getActiveFcmTokens();
                if (!empty($tokens)) {
                    $fcm = FcmService::getInstance();
                    $fcmBody = "You have been assigned {$count} doctor contact(s) for the active visit cycle.";
                    if ($scheduledVisitsCount > 0) {
                        $fcmBody .= " {$scheduledVisitsCount} visit(s) have been scheduled on your agenda.";
                    }
                    $fcm->sendToTokens(
                        $tokens,
                        'New Contacts Assigned',
                        $fcmBody,
                        ['type' => 'new_assignments', 'cycle_id' => (string) $cycleId]
                    );
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to send FCM assignment alert: " . $e->getMessage());
        }

        return [
            'assigned_count' => $count,
            'scheduled_count' => $scheduledVisitsCount,
        ];
    }

    /**
     * Remove or deactivate an assignment
     */
    public function unassignContact(int $assignmentId): bool
    {
        $assignment = ContactAssignment::findOrFail($assignmentId);
        return (bool) $assignment->delete();
    }
}
