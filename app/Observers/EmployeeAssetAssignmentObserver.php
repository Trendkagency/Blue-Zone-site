<?php

namespace App\Observers;

use App\Models\EmployeeAssetAssignment;

class EmployeeAssetAssignmentObserver
{
    public function created(EmployeeAssetAssignment $assignment): void
    {
        $assignment->asset?->update(['status' => 'assigned']);
    }

    public function updated(EmployeeAssetAssignment $assignment): void
    {
        if ($assignment->wasChanged('returned_at') && !empty($assignment->returned_at)) {
            $newStatus = ($assignment->condition_on_return === 'damaged') ? 'maintenance' : 'available';
            $assignment->asset?->update(['status' => $newStatus]);
        }
    }
}
