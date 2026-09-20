<?php

namespace App\Services\Mr;

use App\Models\Mr\ContactAssignment;
use App\Models\Mr\ScheduledVisit;
use App\Models\Mr\VisitCycle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class CrmScheduleService
{
    /**
     * Create a new scheduled visit on the MR's calendar
     */
    public function scheduleVisit(int $assignmentId, string|Carbon $dateTime, ?string $notes = null): ScheduledVisit
    {
        $assignment = ContactAssignment::with(['contact', 'cycle'])->findOrFail($assignmentId);
        $scheduledAt = Carbon::parse($dateTime);

        return ScheduledVisit::create([
            'assignment_id' => $assignment->id,
            'mr_id' => $assignment->mr_id,
            'contact_id' => $assignment->contact_id,
            'cycle_id' => $assignment->cycle_id,
            'scheduled_at' => $scheduledAt,
            'status' => 'planned',
            'notes' => $notes,
        ]);
    }

    /**
     * Reschedule an existing planned visit slot
     */
    public function rescheduleVisit(int $scheduledVisitId, string|Carbon $newDateTime, ?string $notes = null): ScheduledVisit
    {
        $scheduled = ScheduledVisit::findOrFail($scheduledVisitId);
        $scheduled->update([
            'scheduled_at' => Carbon::parse($newDateTime),
            'status' => 'rescheduled',
            'notes' => $notes ?? $scheduled->notes,
        ]);

        return $scheduled;
    }

    /**
     * Cancel a planned visit slot
     */
    public function cancelScheduledVisit(int $scheduledVisitId, ?string $reason = null): bool
    {
        $scheduled = ScheduledVisit::findOrFail($scheduledVisitId);
        return $scheduled->update([
            'status' => 'cancelled',
            'notes' => $reason ? ($scheduled->notes . " | Reason: " . $reason) : $scheduled->notes,
        ]);
    }

    /**
     * Proactively find at-risk contact assignments for a representative in a cycle
     * An assignment is at risk if (target_visits - visits_done) > 0 and cycle remaining days is tight.
     */
    public function getAtRiskAssignmentsForRep(int $mrId, ?int $cycleId = null): Collection
    {
        $cycle = $cycleId ? VisitCycle::find($cycleId) : VisitCycle::where('status', 'active')->first();
        if (!$cycle) {
            return new Collection();
        }

        $daysRemaining = $cycle->getDaysRemaining();

        $assignments = ContactAssignment::with(['contact.specialty', 'contact.classification', 'cycle'])
            ->where('mr_id', $mrId)
            ->where('cycle_id', $cycle->id)
            ->where('is_active', true)
            ->get();

        return $assignments->filter(function (ContactAssignment $assignment) use ($daysRemaining) {
            $needed = $assignment->target_visits - $assignment->visits_done;
            if ($needed <= 0) {
                return false;
            }

            // Flag as at-risk if remaining days are fewer than or equal to 3 days per visit needed
            return $daysRemaining <= ($needed * 3) || $daysRemaining <= 7;
        })->sortByDesc(function (ContactAssignment $assignment) {
            // Sort by class priority and remaining visits needed
            $classPoints = $assignment->contact?->classification?->points ?? 1;
            $needed = $assignment->target_visits - $assignment->visits_done;
            return $classPoints * $needed;
        })->values();
    }

    /**
     * Get calendar events format for MR scheduling calendar
     */
    public function getCalendarEventsForRep(int $mrId, string|Carbon $startDate, string|Carbon $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $scheduledVisits = ScheduledVisit::with(['contact.classification', 'contact.specialty'])
            ->where('mr_id', $mrId)
            ->whereBetween('scheduled_at', [$start, $end])
            ->get();

        return $scheduledVisits->map(function (ScheduledVisit $sv) {
            $classCode = $sv->contact?->classification?->code ?? 'C';
            $color = match ($classCode) {
                'A+' => '#dc2626', // Red (High Priority)
                'A' => '#ea580c',  // Orange
                'B' => '#2563eb',  // Blue
                'C' => '#16a34a',  // Green
                default => '#6b7280', // Grey
            };

            return [
                'id' => $sv->id,
                'title' => ($sv->contact?->name ?? 'Doctor') . " (" . $classCode . ")",
                'start' => $sv->scheduled_at->toIso8601String(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'status' => $sv->status,
                'contact_id' => $sv->contact_id,
                'doctor_name' => $sv->contact?->name,
                'specialty' => $sv->contact?->specialty?->name,
                'clinic' => $sv->contact?->hospital_clinic_name,
                'address' => $sv->contact?->address,
                'lat' => (float) $sv->contact?->latitude,
                'lng' => (float) $sv->contact?->longitude,
            ];
        })->toArray();
    }
}
