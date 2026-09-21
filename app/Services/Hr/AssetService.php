<?php

namespace App\Services\Hr;

use App\Models\Employee;
use App\Models\EmployeeAssetAssignment;
use App\Models\HrAsset;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AssetService
{
    /**
     * Assign a company asset to an employee, updating asset status in transaction.
     */
    public function assignAsset(HrAsset $asset, Employee $employee, array $data, ?int $assignedByUserId = null): EmployeeAssetAssignment
    {
        return DB::transaction(function () use ($asset, $employee, $data, $assignedByUserId) {
            if ($asset->status === 'assigned') {
                throw new InvalidArgumentException("Asset {$asset->asset_code} ({$asset->name}) is already assigned to another employee.");
            }

            $assignment = EmployeeAssetAssignment::create([
                'employee_id' => $employee->id,
                'hr_asset_id' => $asset->id,
                'assigned_at' => $data['assigned_at'] ?? now(),
                'expected_return_date' => $data['expected_return_date'] ?? null,
                'condition_on_assignment' => $data['condition_on_assignment'] ?? 'good',
                'notes' => $data['notes'] ?? null,
                'assigned_by' => $assignedByUserId,
            ]);

            $asset->update(['status' => 'assigned']);

            return $assignment;
        });
    }

    /**
     * Return an assigned asset, auditing its condition and making asset available again.
     */
    public function returnAsset(EmployeeAssetAssignment $assignment, array $returnData): bool
    {
        return DB::transaction(function () use ($assignment, $returnData) {
            $assignment->update([
                'returned_at' => $returnData['returned_at'] ?? now(),
                'condition_on_return' => $returnData['condition_on_return'] ?? 'good',
                'notes' => !empty($returnData['notes']) ? ($assignment->notes . "\nReturn: " . $returnData['notes']) : $assignment->notes,
            ]);

            $newStatus = ($returnData['condition_on_return'] ?? '') === 'damaged' ? 'maintenance' : 'available';
            $assignment->asset?->update(['status' => $newStatus]);

            return true;
        });
    }
}
