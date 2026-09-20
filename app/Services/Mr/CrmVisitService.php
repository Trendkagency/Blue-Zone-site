<?php

namespace App\Services\Mr;

use App\Models\Mr\Contact;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\ScheduledVisit;
use App\Models\Mr\Visit;
use App\Models\Mr\VisitCycle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CrmVisitService
{
    protected GpsValidationService $gpsService;

    public function __construct(?GpsValidationService $gpsService = null)
    {
        $this->gpsService = $gpsService ?? GpsValidationService::getInstance();
    }

    /**
     * Submit a Check-In for an MR visit
     *
     * @param int $mrId The representative's user ID
     * @param array $data [
     *   'contact_id' => int,
     *   'scheduled_visit_id' => ?int,
     *   'assignment_id' => ?int,
     *   'cycle_id' => ?int,
     *   'lat' => ?float,
     *   'lng' => ?float,
     *   'accuracy_m' => ?float,
     *   'device_meta' => ?array
     * ]
     * @return Visit
     */
    public function submitCheckIn(int $mrId, array $data): Visit
    {
        $contactId = (int) ($data['contact_id'] ?? 0);
        $contact = Contact::findOrFail($contactId);

        // Find or infer active cycle
        $cycleId = $data['cycle_id'] ?? null;
        if (!$cycleId) {
            $cycle = VisitCycle::where('status', 'active')->first();
            $cycleId = $cycle?->id;
        }

        if (!$cycleId) {
            throw new InvalidArgumentException("No active visit cycle found.");
        }

        // Find or infer contact assignment
        $assignmentId = $data['assignment_id'] ?? null;
        if (!$assignmentId) {
            $assignment = ContactAssignment::where('cycle_id', $cycleId)
                ->where('mr_id', $mrId)
                ->where('contact_id', $contactId)
                ->first();
            $assignmentId = $assignment?->id;
        }

        $scheduledVisitId = $data['scheduled_visit_id'] ?? null;

        // Perform GPS validation server-side
        $lat = isset($data['lat']) ? (float) $data['lat'] : null;
        $lng = isset($data['lng']) ? (float) $data['lng'] : null;
        $accuracy = isset($data['accuracy_m']) ? (float) $data['accuracy_m'] : null;
        $deviceMeta = $data['device_meta'] ?? [];

        $validation = $this->gpsService->validateCheckIn(
            $lat,
            $lng,
            $contact->latitude ? (float) $contact->latitude : null,
            $contact->longitude ? (float) $contact->longitude : null,
            $mrId,
            $deviceMeta
        );

        return DB::transaction(function () use (
            $mrId,
            $contactId,
            $cycleId,
            $assignmentId,
            $scheduledVisitId,
            $lat,
            $lng,
            $accuracy,
            $deviceMeta,
            $validation
        ) {
            $visit = Visit::create([
                'scheduled_visit_id' => $scheduledVisitId,
                'assignment_id' => $assignmentId,
                'mr_id' => $mrId,
                'contact_id' => $contactId,
                'cycle_id' => $cycleId,
                'checkin_at' => now(),
                'checkin_lat' => $lat,
                'checkin_lng' => $lng,
                'checkin_accuracy_m' => $accuracy,
                'distance_from_contact_m' => $validation['distance_m'],
                'gps_verified' => (bool) $validation['verified'],
                'gps_flag' => $validation['flag'],
                'device_meta' => $deviceMeta,
            ]);

            return $visit;
        });
    }

    /**
     * Submit Check-Out for an ongoing visit
     *
     * @param int $visitId
     * @param int $mrId
     * @param array $data [
     *   'lat' => ?float,
     *   'lng' => ?float,
     *   'outcome' => ?string,
     *   'notes' => ?string
     * ]
     * @return Visit
     */
    public function submitCheckOut(int $visitId, int $mrId, array $data): Visit
    {
        $visit = Visit::where('id', $visitId)
            ->where('mr_id', $mrId)
            ->firstOrFail();

        $checkoutAt = now();
        $checkinAt = $visit->checkin_at;
        $durationMinutes = $checkinAt ? max(1, (int) $checkinAt->diffInMinutes($checkoutAt)) : null;

        $checkoutLat = isset($data['lat']) ? (float) $data['lat'] : null;
        $checkoutLng = isset($data['lng']) ? (float) $data['lng'] : null;
        $outcome = $data['outcome'] ?? 'completed';
        $notes = $data['notes'] ?? null;

        $visit->update([
            'checkout_at' => $checkoutAt,
            'checkout_lat' => $checkoutLat,
            'checkout_lng' => $checkoutLng,
            'duration_minutes' => $durationMinutes,
            'outcome' => $outcome,
            'notes' => $notes,
        ]);

        if ($visit->scheduled_visit_id) {
            ScheduledVisit::where('id', $visit->scheduled_visit_id)->update([
                'status' => 'completed',
            ]);
        }

        return $visit->fresh();
    }
}
