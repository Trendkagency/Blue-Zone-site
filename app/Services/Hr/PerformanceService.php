<?php

namespace App\Services\Hr;

use App\Models\Employee;
use App\Models\EmployeePromotion;
use App\Models\EmployeeSalaryHistory;
use Illuminate\Support\Facades\DB;

class PerformanceService
{
    /**
     * Execute employee promotion, updating employment details and preserving full historical audit.
     */
    public function executePromotion(Employee $employee, array $promotionData, ?int $approvedByUserId = null): EmployeePromotion
    {
        return DB::transaction(function () use ($employee, $promotionData, $approvedByUserId) {
            $oldDeptId = $employee->department_id;
            $oldPosId = $employee->position_id;
            $oldSalary = (float) $employee->basic_salary;

            $newDeptId = $promotionData['new_department_id'] ?? $oldDeptId;
            $newPosId = $promotionData['new_position_id'] ?? $oldPosId;
            $newSalary = isset($promotionData['new_salary']) ? (float) $promotionData['new_salary'] : $oldSalary;
            $effectiveDate = $promotionData['effective_date'] ?? now()->toDateString();
            $reason = $promotionData['reason'] ?? 'Promotion';

            $promotion = EmployeePromotion::create([
                'employee_id' => $employee->id,
                'old_department_id' => $oldDeptId,
                'new_department_id' => $newDeptId,
                'old_position_id' => $oldPosId,
                'new_position_id' => $newPosId,
                'old_salary' => $oldSalary,
                'new_salary' => $newSalary,
                'effective_date' => $effectiveDate,
                'reason' => $reason,
                'approved_by' => $approvedByUserId,
            ]);

            // Update Employee record
            $employee->update([
                'department_id' => $newDeptId,
                'position_id' => $newPosId,
                'basic_salary' => $newSalary,
            ]);

            // If salary changed, record in salary history
            if (abs($newSalary - $oldSalary) > 0.01) {
                EmployeeSalaryHistory::create([
                    'employee_id' => $employee->id,
                    'old_salary' => $oldSalary,
                    'new_salary' => $newSalary,
                    'effective_date' => $effectiveDate,
                    'reason' => "Promotion: {$reason}",
                    'approved_by' => $approvedByUserId,
                ]);
            }

            return $promotion;
        });
    }
}
