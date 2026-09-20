<?php

namespace App\Services\Mr;

use App\Models\Mr\Contact;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\RepDailyLog;
use App\Models\Mr\RepPerformanceSnapshot;
use App\Models\Mr\Visit;
use App\Models\Mr\VisitCycle;
use App\Models\User;
use App\Services\Mr\Strategies\ClassificationPointStrategy;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class CrmMrReportService
{
    protected ClassificationPointStrategy $pointStrategy;

    public function __construct()
    {
        $this->pointStrategy = new ClassificationPointStrategy();
    }

    /**
     * Recalculate and persist the performance snapshot for a single rep in a cycle
     */
    public function recalculateRepSnapshot(int $mrId, int $cycleId): RepPerformanceSnapshot
    {
        $cycle = VisitCycle::findOrFail($cycleId);

        // 1. All assignments for this rep in this cycle
        $assignments = ContactAssignment::with(['contact.classification', 'contact.specialty'])
            ->where('mr_id', $mrId)
            ->where('cycle_id', $cycleId)
            ->where('is_active', true)
            ->get();

        $totalAssignedContacts = $assignments->count();

        // 2. Planned visits and Target Points
        $plannedVisits = 0;
        $targetPoints = 0;
        $achievedPoints = 0;
        $uniqueVisitedContactIds = [];

        foreach ($assignments as $assignment) {
            $class = $assignment->contact?->classification;
            $reqVisits = $class ? (int) $class->required_visits : (int) $assignment->target_visits;
            $pts = $class ? (int) $class->points : 1;

            $plannedVisits += $reqVisits;
            $targetPoints += ($reqVisits * $pts);

            // Count completed visits for this contact
            $completedForContact = Visit::where('mr_id', $mrId)
                ->where('cycle_id', $cycleId)
                ->where('contact_id', $assignment->contact_id)
                ->whereNotNull('checkout_at')
                ->count();

            if ($completedForContact > 0) {
                $uniqueVisitedContactIds[] = $assignment->contact_id;
            }

            // Strictly capped points: min(completed, required) * points
            $cappedVisits = min($completedForContact, $reqVisits);
            $contactAchievedPoints = $cappedVisits * $pts;
            $achievedPoints += $contactAchievedPoints;

            // Sync assignment record if different
            if ($assignment->visits_done !== $completedForContact || $assignment->achieved_points !== $contactAchievedPoints) {
                $assignment->update([
                    'visits_done' => $completedForContact,
                    'achieved_points' => $contactAchievedPoints,
                ]);
            }
        }

        $uniqueContactsVisited = count(array_unique($uniqueVisitedContactIds));

        // 3. Completed Visits total
        $allVisits = Visit::where('mr_id', $mrId)
            ->where('cycle_id', $cycleId)
            ->get();

        $totalCheckins = $allVisits->count();
        $visitsDone = $allVisits->whereNotNull('checkout_at')->count();
        $verifiedVisits = $allVisits->where('gps_verified', true)->count();

        // 4. Rate calculations
        $coverageRatePct = $totalAssignedContacts > 0
            ? round(($uniqueContactsVisited / $totalAssignedContacts) * 100, 2)
            : 0.0;

        $visitCompliancePct = $plannedVisits > 0
            ? round(($visitsDone / $plannedVisits) * 100, 2)
            : 0.0;

        $gpsAccuracyPct = $totalCheckins > 0
            ? round(($verifiedVisits / $totalCheckins) * 100, 2)
            : 0.0;

        $pointsAchievedPct = $targetPoints > 0
            ? round(($achievedPoints / $targetPoints) * 100, 2)
            : 0.0;

        // 5. Unreported Days Count
        $unreportedDaysCount = $this->calculateUnreportedDaysForRep($mrId, $cycle);

        // 6. Upsert Snapshot
        return RepPerformanceSnapshot::updateOrCreate(
            [
                'mr_id' => $mrId,
                'cycle_id' => $cycleId,
            ],
            [
                'total_assigned_contacts' => $totalAssignedContacts,
                'unique_contacts_visited' => $uniqueContactsVisited,
                'coverage_rate_pct' => $coverageRatePct,
                'planned_visits' => $plannedVisits,
                'visits_done' => $visitsDone,
                'visit_compliance_pct' => $visitCompliancePct,
                'verified_visits' => $verifiedVisits,
                'gps_accuracy_pct' => $gpsAccuracyPct,
                'target_points' => $targetPoints,
                'achieved_points' => $achievedPoints,
                'points_achieved_pct' => $pointsAchievedPct,
                'unreported_days_count' => $unreportedDaysCount,
                'calculated_at' => now(),
            ]
        );
    }

    /**
     * Calculate number of unreported working days in a cycle up to today
     */
    public function calculateUnreportedDaysForRep(int $mrId, VisitCycle $cycle): int
    {
        $startDate = $cycle->start_date->copy();
        $endDate = min(now()->startOfDay(), $cycle->end_date->copy()->startOfDay());

        if ($startDate->greaterThan($endDate)) {
            return 0;
        }

        $period = CarbonPeriod::create($startDate, $endDate);
        $unreportedCount = 0;

        foreach ($period as $date) {
            // Skip weekends (Friday/Saturday or Saturday/Sunday depending on region, standard weekday check)
            if ($date->isFriday()) {
                continue; // Weekly day off
            }

            $logDate = $date->toDateString();
            $log = RepDailyLog::where('mr_id', $mrId)
                ->where('log_date', $logDate)
                ->first();

            if (!$log || !$log->is_reported || $log->total_visits_count === 0) {
                $unreportedCount++;
            }
        }

        return $unreportedCount;
    }

    /**
     * Recalculate snapshots for all active MRs in a cycle
     */
    public function recalculateAllSnapshotsForCycle(int $cycleId): Collection
    {
        $repIds = ContactAssignment::where('cycle_id', $cycleId)
            ->distinct()
            ->pluck('mr_id');

        $snapshots = collect();
        foreach ($repIds as $mrId) {
            $snapshots->push($this->recalculateRepSnapshot((int) $mrId, $cycleId));
        }

        return $snapshots;
    }

    /**
     * Generate Unvisited / Coverage Report data matching exact spec (§8)
     */
    public function getUnvisitedCoverageReport(?int $cycleId = null, ?int $mrId = null): Collection
    {
        $cycle = $cycleId ? VisitCycle::find($cycleId) : VisitCycle::where('status', 'active')->first();
        if (!$cycle) {
            return collect();
        }

        $query = ContactAssignment::with([
            'contact.specialty',
            'contact.classification',
            'contact.city',
            'contact.country',
            'representative',
            'cycle',
        ])->where('cycle_id', $cycle->id)->where('is_active', true);

        if ($mrId) {
            $query->where('mr_id', $mrId);
        }

        return $query->get()->map(function (ContactAssignment $assignment) {
            $contact = $assignment->contact;
            $class = $contact?->classification;
            $rep = $assignment->representative;

            $reqVisits = $class ? (int) $class->required_visits : (int) $assignment->target_visits;
            $pointsPerVisit = $class ? (int) $class->points : 1;

            $targetPoints = $reqVisits * $pointsPerVisit;
            $visitsDone = (int) $assignment->visits_done;
            $achievedPoints = min($visitsDone, $reqVisits) * $pointsPerVisit;
            $compliancePct = $reqVisits > 0 ? round(($visitsDone / $reqVisits) * 100, 2) : 0.0;

            return [
                'assignment_id' => $assignment->id,
                'contact_code' => $contact?->code ?? 'N/A',
                'contact_name' => $contact?->name ?? 'N/A',
                'region_city' => ($contact?->region ? $contact->region . ', ' : '') . ($contact?->city?->name ?? 'N/A'),
                'specialty' => $contact?->specialty?->name ?? 'General',
                'address' => $contact?->address ?? 'N/A',
                'class' => $class?->code ?? 'C',
                'required_frequency' => $reqVisits,
                'assigned_user' => $rep?->name ?? 'Unassigned',
                'mr_id' => $assignment->mr_id,
                'visits_done' => $visitsDone,
                'target_points' => $targetPoints,
                'achieved_points' => $achievedPoints,
                'visit_compliance_pct' => $compliancePct,
                'compliance_pct' => $compliancePct,
                'is_unvisited' => ($visitsDone === 0),
                'is_behind' => ($visitsDone < $reqVisits),
            ];
        });
    }

    /**
     * Generate Rep Performance Report data matching exact spec (§8)
     */
    public function getRepPerformanceReport(?int $cycleId = null): Collection
    {
        $cycle = $cycleId ? VisitCycle::find($cycleId) : VisitCycle::where('status', 'active')->first();
        if (!$cycle) {
            return collect();
        }

        // Get snapshots, or calculate if missing
        $reps = User::whereHas('mrAssignments', function ($q) use ($cycle) {
            $q->where('cycle_id', $cycle->id);
        })->get();

        return $reps->map(function (User $rep) use ($cycle) {
            $snapshot = RepPerformanceSnapshot::where('mr_id', $rep->id)
                ->where('cycle_id', $cycle->id)
                ->first();

            if (!$snapshot || $snapshot->calculated_at->diffInMinutes(now()) > 60) {
                $snapshot = $this->recalculateRepSnapshot($rep->id, $cycle->id);
            }

            return [
                'mr_id' => $rep->id,
                'rep_name' => $rep->name,
                'rep_email' => $rep->email,
                'total_assigned_contacts' => $snapshot->total_assigned_contacts,
                'unique_contacts_visited' => $snapshot->unique_contacts_visited,
                'coverage_rate_pct' => (float) $snapshot->coverage_rate_pct,
                'visits_done' => $snapshot->visits_done,
                'planned_visits' => $snapshot->planned_visits,
                'planned_vs_done' => "{$snapshot->visits_done} / {$snapshot->planned_visits}",
                'accuracy_pct' => (float) $snapshot->gps_accuracy_pct,
                'visit_compliance_pct' => (float) $snapshot->visit_compliance_pct,
                'target_points' => $snapshot->target_points,
                'achieved_points' => $snapshot->achieved_points,
                'points_achieved_pct' => (float) $snapshot->points_achieved_pct,
                'target_vs_achieved_points' => "{$snapshot->achieved_points} / {$snapshot->target_points}",
                'unreported_days_count' => $snapshot->unreported_days_count,
                'calculated_at' => $snapshot->calculated_at->toDateTimeString(),
            ];
        });
    }
}
