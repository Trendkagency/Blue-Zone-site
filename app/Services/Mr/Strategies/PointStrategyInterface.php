<?php

namespace App\Services\Mr\Strategies;

use App\Models\Mr\Contact;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\ContactClassification;

interface PointStrategyInterface
{
    /**
     * Calculate target points for an assigned contact
     */
    public function calculateTargetPoints(Contact $contact, ?ContactClassification $classification = null): int;

    /**
     * Calculate achieved points for an assignment given the number of completed visits
     * Strictly enforces per-contact capping: visits exceeding required_visits do not add points.
     */
    public function calculateAchievedPoints(ContactAssignment $assignment, int $completedVisits): int;
}
