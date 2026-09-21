<?php

namespace App\Services\Hr;

use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\EmployeeOffboarding;
use App\Models\FinalSettlement;
use App\Models\LeaveBalance;
use App\Models\SalaryAdvance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OffboardingService
{
    /**
     * Submit resignation request for an employee.
     */
    public function submitResignation(Employee $employee, array $data): EmployeeOffboarding
    {
        return DB::transaction(function () use ($employee, $data) {
            $submissionDate = $data['submission_date'] ?? now()->toDateString();
            $noticeDays = (int) ($data['notice_period_days'] ?? 30);
            $lastWorkingDate = $data['last_working_date'] ?? Carbon::parse($submissionDate)->addDays($noticeDays)->toDateString();

            return EmployeeOffboarding::create([
                'employee_id' => $employee->id,
                'offboarding_type' => 'resignation',
                'submission_date' => $submissionDate,
                'last_working_date' => $lastWorkingDate,
                'reason' => $data['reason'] ?? 'Resignation submitted',
                'notice_period_days' => $noticeDays,
                'status' => 'submitted',
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    /**
     * Process official termination for an employee.
     */
    public function processTermination(Employee $employee, array $data, ?int $approvedByUserId = null): EmployeeOffboarding
    {
        return DB::transaction(function () use ($employee, $data, $approvedByUserId) {
            $terminationDate = $data['last_working_date'] ?? now()->toDateString();

            $offboarding = EmployeeOffboarding::create([
                'employee_id' => $employee->id,
                'offboarding_type' => 'termination',
                'submission_date' => now()->toDateString(),
                'last_working_date' => $terminationDate,
                'reason' => $data['reason'] ?? 'Company termination',
                'notice_period_days' => (int) ($data['notice_period_days'] ?? 0),
                'status' => 'approved',
                'notes' => $data['notes'] ?? null,
                'approved_by' => $approvedByUserId,
            ]);

            return $offboarding;
        });
    }

    /**
     * Calculate comprehensive final settlement (gratuity, leave encashment, loan/advance offsets).
     */
    public function calculateFinalSettlement(EmployeeOffboarding $offboarding, ?int $approvedByUserId = null): FinalSettlement
    {
        return DB::transaction(function () use ($offboarding, $approvedByUserId) {
            $employee = $offboarding->employee;
            $basic = (float) $employee->basic_salary;
            $dailyRate = $basic > 0 ? ($basic / 30) : 0;

            // 1. Leave Encashment
            $currentYear = (int) now()->year;
            $annualLeaveBalance = LeaveBalance::where('employee_id', $employee->id)
                ->where('year', $currentYear)
                ->whereHas('leaveType', fn ($q) => $q->where('code', 'annual'))
                ->first();

            $remainingLeaveDays = $annualLeaveBalance ? (float) $annualLeaveBalance->remaining_days : 0;
            $leaveEncashment = round($remainingLeaveDays * $dailyRate, 2);

            // 2. End of Service Gratuity calculation (based on tenure years)
            $tenureYears = 0.0;
            if ($employee->hire_date) {
                $lastDay = Carbon::parse($offboarding->last_working_date);
                $tenureYears = max(0.0, round($employee->hire_date->diffInDays($lastDay) / 365, 2));
            }

            $gratuity = 0.00;
            if ($tenureYears >= 2) {
                // Half month salary for first 5 years, one month salary thereafter
                if ($tenureYears <= 5) {
                    $gratuity = round($tenureYears * ($basic / 2), 2);
                } else {
                    $firstFive = 5 * ($basic / 2);
                    $remainingYears = $tenureYears - 5;
                    $gratuity = round($firstFive + ($remainingYears * $basic), 2);
                }

                // If resignation, statutory percentage applies
                if ($offboarding->offboarding_type === 'resignation') {
                    if ($tenureYears < 5) {
                        $gratuity = round($gratuity * (1 / 3), 2);
                    } elseif ($tenureYears < 10) {
                        $gratuity = round($gratuity * (2 / 3), 2);
                    }
                }
            }

            // 3. Outstanding loan & advance deductions
            $loanBalance = (float) EmployeeLoan::where('employee_id', $employee->id)
                ->where('status', 'active')
                ->sum('remaining_balance');

            $advanceBalance = (float) SalaryAdvance::where('employee_id', $employee->id)
                ->whereIn('status', ['approved', 'active'])
                ->sum('remaining_amount');

            $totalEarnings = $leaveEncashment + $gratuity;
            $totalDeductions = $loanBalance + $advanceBalance;
            $netSettlement = max(0.00, $totalEarnings - $totalDeductions);

            return FinalSettlement::updateOrCreate(
                [
                    'offboarding_id' => $offboarding->id,
                    'employee_id' => $employee->id,
                ],
                [
                    'basic_salary_due' => 0.00,
                    'leave_encashment_amount' => $leaveEncashment,
                    'overtime_amount' => 0.00,
                    'end_of_service_gratuity' => $gratuity,
                    'advance_deductions' => $advanceBalance,
                    'loan_deductions' => $loanBalance,
                    'asset_damage_deductions' => 0.00,
                    'net_settlement_amount' => $netSettlement,
                    'status' => 'draft',
                    'settlement_date' => now()->toDateString(),
                    'approved_by' => $approvedByUserId,
                ]
            );
        });
    }

    /**
     * Complete offboarding, deactivate user account (if any), and mark employee as resigned/terminated.
     */
    public function completeOffboarding(EmployeeOffboarding $offboarding, ?int $completedByUserId = null): bool
    {
        return DB::transaction(function () use ($offboarding, $completedByUserId) {
            $employee = $offboarding->employee;

            $status = $offboarding->offboarding_type === 'resignation' ? 'resigned' : 'terminated';
            $employee->update(['employment_status' => $status]);

            // Deactivate system login if user exists
            if ($employee->user) {
                $employee->user->update(['status' => 'inactive']);
            }

            $offboarding->update([
                'status' => 'completed',
                'approved_by' => $completedByUserId ?? $offboarding->approved_by,
            ]);

            return true;
        });
    }
}
