<?php

namespace App\Services\Hr;

use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\EmployeeSalaryHistory;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    protected EmployeeNumberService $numberService;

    public function __construct(?EmployeeNumberService $numberService = null)
    {
        $this->numberService = $numberService ?? EmployeeNumberService::getInstance();
    }

    /**
     * Create an employee with optional initial contract and leave balances in a transaction.
     */
    public function createEmployee(array $data, ?int $createdByUserId = null): Employee
    {
        return DB::transaction(function () use ($data, $createdByUserId) {
            if (empty($data['employee_number'])) {
                $data['employee_number'] = $this->numberService->generateNextNumber();
            }

            $employee = Employee::create($data);

            // Initial salary history entry
            if (!empty($employee->basic_salary) && (float)$employee->basic_salary > 0) {
                EmployeeSalaryHistory::create([
                    'employee_id' => $employee->id,
                    'old_salary' => 0.00,
                    'new_salary' => $employee->basic_salary,
                    'effective_date' => $employee->hire_date ?? now()->toDateString(),
                    'reason' => 'Initial Employment Offer',
                    'approved_by' => $createdByUserId,
                ]);
            }

            // Create Initial Contract if contract start date is present
            if (!empty($data['contract_start_date'])) {
                EmployeeContract::create([
                    'employee_id' => $employee->id,
                    'contract_number' => 'CNT-' . $employee->employee_number,
                    'contract_type' => $data['contract_type'] ?? 'fixed_term',
                    'start_date' => $data['contract_start_date'],
                    'end_date' => $data['contract_end_date'] ?? null,
                    'basic_salary' => $employee->basic_salary,
                    'housing_allowance' => $employee->housing_allowance ?? 0,
                    'transportation_allowance' => $employee->transportation_allowance ?? 0,
                    'other_allowance' => $employee->other_allowance ?? 0,
                    'probation_days' => 90,
                    'notice_period_days' => 30,
                    'status' => 'active',
                    'created_by' => $createdByUserId,
                ]);
            }

            // Initialize leave balances for active leave types
            $currentYear = (int) now()->year;
            $leaveTypes = LeaveType::where('is_active', true)->get();
            foreach ($leaveTypes as $type) {
                LeaveBalance::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'leave_type_id' => $type->id,
                        'year' => $currentYear,
                    ],
                    [
                        'allocated_days' => $type->annual_days,
                        'used_days' => 0.00,
                        'pending_days' => 0.00,
                        'remaining_days' => $type->annual_days,
                    ]
                );
            }

            return $employee;
        });
    }

    /**
     * Update employee profile and handle salary history if salary changed.
     */
    public function updateEmployee(Employee $employee, array $data, ?int $updatedByUserId = null): Employee
    {
        return DB::transaction(function () use ($employee, $data, $updatedByUserId) {
            $oldSalary = (float) $employee->basic_salary;

            $employee->update($data);

            if (isset($data['basic_salary'])) {
                $newSalary = (float) $data['basic_salary'];
                if (abs($newSalary - $oldSalary) > 0.01) {
                    EmployeeSalaryHistory::create([
                        'employee_id' => $employee->id,
                        'old_salary' => $oldSalary,
                        'new_salary' => $newSalary,
                        'effective_date' => now()->toDateString(),
                        'reason' => $data['salary_change_reason'] ?? 'Salary Adjustment',
                        'approved_by' => $updatedByUserId,
                    ]);
                }
            }

            return $employee;
        });
    }

    /**
     * Get aggregated chronological timeline for an employee.
     */
    public function getTimeline(Employee $employee): array
    {
        $timeline = [];

        // 1. Hire date
        if ($employee->hire_date) {
            $timeline[] = [
                'type' => 'hire',
                'title' => 'Joined Company',
                'date' => $employee->hire_date->format('Y-m-d'),
                'description' => "Joined as {$employee->position?->name} in {$employee->department?->name}",
                'icon' => 'fa-solid fa-user-plus',
                'badge_class' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
            ];
        }

        // 2. Contracts
        foreach ($employee->contracts as $contract) {
            $timeline[] = [
                'type' => 'contract',
                'title' => "Contract {$contract->contract_number} ({$contract->contract_type})",
                'date' => $contract->start_date?->format('Y-m-d') ?? $contract->created_at->format('Y-m-d'),
                'description' => "Status: {$contract->status}, Start: " . ($contract->start_date?->format('Y-m-d') ?? 'N/A') . ', End: ' . ($contract->end_date?->format('Y-m-d') ?? 'Ongoing'),
                'icon' => 'fa-solid fa-file-signature',
                'badge_class' => 'bg-sky-500/10 text-sky-400 border border-sky-500/20',
            ];
        }

        // 3. Salary History
        foreach ($employee->salaryHistory as $sal) {
            $timeline[] = [
                'type' => 'salary',
                'title' => 'Salary Revision',
                'date' => $sal->effective_date?->format('Y-m-d') ?? $sal->created_at->format('Y-m-d'),
                'description' => number_format((float)$sal->old_salary, 2) . ' SAR → ' . number_format((float)$sal->new_salary, 2) . ' SAR (' . ($sal->reason ?: 'Adjustment') . ')',
                'icon' => 'fa-solid fa-money-bill-wave',
                'badge_class' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            ];
        }

        // 4. Promotions
        foreach ($employee->promotions as $promo) {
            $timeline[] = [
                'type' => 'promotion',
                'title' => 'Promotion / Transfer',
                'date' => $promo->effective_date?->format('Y-m-d') ?? $promo->created_at->format('Y-m-d'),
                'description' => "Promoted to {$promo->newPosition?->name} ({$promo->newDepartment?->name}). Reason: {$promo->reason}",
                'icon' => 'fa-solid fa-award',
                'badge_class' => 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
            ];
        }

        // 5. Disciplinary
        foreach ($employee->disciplinaryActions as $action) {
            $timeline[] = [
                'type' => 'disciplinary',
                'title' => 'Disciplinary: ' . ucfirst(str_replace('_', ' ', $action->action_type)),
                'date' => $action->action_date?->format('Y-m-d') ?? $action->created_at->format('Y-m-d'),
                'description' => "Incident on {$action->incident_date?->format('Y-m-d')}: {$action->description}",
                'icon' => 'fa-solid fa-triangle-exclamation',
                'badge_class' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
            ];
        }

        // 6. Assets
        foreach ($employee->assetAssignments as $assign) {
            $timeline[] = [
                'type' => 'asset',
                'title' => 'Asset Assigned: ' . $assign->asset?->name,
                'date' => $assign->assigned_at?->format('Y-m-d') ?? $assign->created_at->format('Y-m-d'),
                'description' => "Code: {$assign->asset?->asset_code}, Serial: {$assign->asset?->serial_number}",
                'icon' => 'fa-solid fa-laptop',
                'badge_class' => 'bg-teal-500/10 text-teal-400 border border-teal-500/20',
            ];
        }

        // 7. Offboarding
        if ($employee->offboarding) {
            $off = $employee->offboarding;
            $timeline[] = [
                'type' => 'offboarding',
                'title' => 'Offboarding: ' . ucfirst($off->offboarding_type),
                'date' => $off->last_working_date?->format('Y-m-d') ?? $off->submission_date?->format('Y-m-d'),
                'description' => "Status: {$off->status}. Reason: {$off->reason}",
                'icon' => 'fa-solid fa-person-walking-arrow-right',
                'badge_class' => 'bg-red-500/10 text-red-400 border border-red-500/20',
            ];
        }

        // Sort timeline descending by date
        usort($timeline, fn ($a, $b) => strcmp($b['date'], $a['date']));

        return $timeline;
    }
}
