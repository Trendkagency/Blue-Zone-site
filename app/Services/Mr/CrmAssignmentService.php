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
     * Bulk assign contacts to an MR for a cycle
     *
     * @param int $cycleId
     * @param int $mrId
     * @param array<int> $contactIds
     * @return int Count of created/updated assignments
     */
    public function bulkAssignContacts(int $cycleId, int $mrId, array $contactIds): int
    {
        $count = 0;
        DB::transaction(function () use ($cycleId, $mrId, $contactIds, &$count) {
            foreach ($contactIds as $contactId) {
                $this->assignContact($cycleId, $mrId, (int) $contactId);
                $count++;
            }
        });

        // Trigger FCM push notification to MR
        try {
            $user = User::find($mrId);
            if ($user && method_exists($user, 'getActiveFcmTokens')) {
                $tokens = $user->getActiveFcmTokens();
                if (!empty($tokens)) {
                    $fcm = FcmService::getInstance();
                    $fcm->sendToTokens(
                        $tokens,
                        'New Contacts Assigned',
                        "You have been assigned {$count} doctor contact(s) for the active visit cycle.",
                        ['type' => 'new_assignments', 'cycle_id' => (string) $cycleId]
                    );
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to send FCM assignment alert: " . $e->getMessage());
        }

        return $count;
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
