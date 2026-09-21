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
     * Automatically generate scheduled visits for a batch of contact assignments.
     * Supports Weekly, Monthly, and Daily cadence, ensuring visits for today are generated immediately.
     *
     * @param array<ContactAssignment|int> $assignments
     * @param array $options [cadence, start_date, schedule_today, time, notes]
     * @return int Count of scheduled visits created
     */
    public function generateAutoScheduleForAssignments(array $assignments, array $options = []): int
    {
        $cadence = $options['cadence'] ?? 'weekly';
        $startDateInput = $options['start_date'] ?? now()->toDateString();
        $baseDate = Carbon::parse($startDateInput)->startOfDay();
        $scheduleToday = (bool) ($options['schedule_today'] ?? true);
        $includeWeekends = (bool) ($options['include_weekends'] ?? $options['daily_all_days'] ?? false);
        $baseTime = $options['time'] ?? '09:30';
        $notes = $options['notes'] ?? 'Auto-scheduled assignment visit';

        // Parse hour & minute
        $timeParts = explode(':', $baseTime);
        $startHour = (int) ($timeParts[0] ?? 9);
        $startMinute = (int) ($timeParts[1] ?? 30);

        $createdCount = 0;
        $daySlots = [];

        foreach ($assignments as $item) {
            $assignment = $item instanceof ContactAssignment
                ? $item
                : ContactAssignment::with(['contact.classification', 'cycle'])->find($item);

            if (!$assignment) {
                continue;
            }

            $contact = $assignment->contact;
            $class = $contact?->classification;
            $neededVisits = $class ? (int) $class->required_visits : (int) $assignment->target_visits;
            if (!empty($options['daily_count'])) {
                $neededVisits = max(1, (int) $options['daily_count']);
            }
            $neededVisits = max(1, $neededVisits);

            $cycle = $assignment->cycle;
            $cycleEnd = $cycle?->end_date ? Carbon::parse($cycle->end_date)->endOfDay() : null;

            for ($visitIndex = 0; $visitIndex < $neededVisits; $visitIndex++) {
                $targetDate = $baseDate->copy();

                if ($visitIndex === 0 && $scheduleToday) {
                    // Initial visit set to today so MR sees it immediately in Today's Field Agenda
                    $targetDate = now()->startOfDay();
                } else {
                    $offset = $visitIndex;
                    if ($scheduleToday && !$baseDate->isToday() && $visitIndex > 0) {
                        $offset = $visitIndex - 1;
                    }

                    if ($cadence === 'daily') {
                        $targetDate->addDays($offset);
                        // Skip Friday/Saturday if weekend and not configured for all days
                        if (!$includeWeekends) {
                            if ($targetDate->isFriday()) {
                                $targetDate->addDays(2);
                            } elseif ($targetDate->isSaturday()) {
                                $targetDate->addDays(1);
                            }
                        }
                    } elseif ($cadence === 'weekly') {
                        $targetDate->addWeeks($offset);
                    } elseif ($cadence === 'monthly') {
                        $daysStep = max(7, (int) round(28 / max(1, $neededVisits)));
                        $targetDate->addDays($offset * $daysStep);
                    }
                }

                // Clamp if beyond cycle end
                if ($cycleEnd && $targetDate->gt($cycleEnd) && $cycleEnd->gt(now())) {
                    $targetDate = $cycleEnd->copy()->subDays($visitIndex % 3);
                }

                // Stagger daytime hours on this date
                $dateKey = $targetDate->toDateString();
                $slotOnThisDay = $daySlots[$dateKey] ?? 0;
                $daySlots[$dateKey] = $slotOnThisDay + 1;

                $slotMinutesOffset = ($slotOnThisDay % 6) * 60;
                if ($startHour * 60 + $startMinute + $slotMinutesOffset >= 780 && $slotOnThisDay >= 3) {
                    $slotMinutesOffset += 30;
                }

                $visitDateTime = $targetDate->copy()
                    ->setTime($startHour, $startMinute)
                    ->addMinutes($slotMinutesOffset);

                // Check duplicate planned visit on same day
                $alreadyExists = ScheduledVisit::where('assignment_id', $assignment->id)
                    ->whereDate('scheduled_at', $visitDateTime->toDateString())
                    ->where('status', 'planned')
                    ->exists();

                if (!$alreadyExists) {
                    ScheduledVisit::create([
                        'assignment_id' => $assignment->id,
                        'mr_id' => $assignment->mr_id,
                        'contact_id' => $assignment->contact_id,
                        'cycle_id' => $assignment->cycle_id,
                        'scheduled_at' => $visitDateTime,
                        'status' => 'planned',
                        'notes' => $notes . " (Visit #" . ($visitIndex + 1) . "/{$neededVisits} • " . ucfirst($cadence) . ")",
                    ]);
                    $createdCount++;
                }
            }
        }

        return $createdCount;
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
