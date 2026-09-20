<?php

namespace App\Services\Mr\Strategies;

use App\Models\Mr\Contact;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\ContactClassification;

class ClassificationPointStrategy implements PointStrategyInterface
{
    /**
     * Target points = points * required_visits (maximum points achievable if all required visits are made)
     */
    public function calculateTargetPoints(Contact $contact, ?ContactClassification $classification = null): int
    {
        $class = $classification ?? $contact->classification;
        if (!$class) {
            return 0;
        }

        // Each required visit earns $class->points
        return (int) ($class->points * $class->required_visits);
    }

    /**
     * Achieved points = min(visits_completed, required_visits) * class.points
     * Strict per-contact capping: extra visits beyond requirement do not inflate points.
     */
    public function calculateAchievedPoints(ContactAssignment $assignment, int $completedVisits): int
    {
        $contact = $assignment->contact;
        $class = $contact?->classification;

        if (!$class) {
            return 0;
        }

        $requiredVisits = (int) ($class->required_visits > 0 ? $class->required_visits : $assignment->target_visits);
        $pointsPerVisit = (int) $class->points;

        $cappedVisits = min($completedVisits, $requiredVisits);

        return $cappedVisits * $pointsPerVisit;
    }
}
