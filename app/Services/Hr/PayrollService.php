<?php

namespace App\Services\Hr;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\OvertimeRequest;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\Payslip;
use App\Models\SalaryAdvance;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PayrollService
{
    /**
     * Generate payroll calculations for all active employees within a payroll period.
     */
    public function generatePeriodPayroll(PayrollPeriod $period): array
    {
        if (in_array($period->status, ['finalized', 'paid'], true)) {
            throw new InvalidArgumentException("Cannot recalculate or modify a finalized payroll period.");
        }

        return DB::transaction(function () use ($period) {
            $period->update(['status' => 'processing']);

            $employees = Employee::whereIn('employment_status', ['active', 'probation'])->get();
            $periodGross = 0.00;
            $periodDeductions = 0.00;
            $periodNet = 0.00;

            foreach ($employees as $emp) {
                $basic = (float) $emp->basic_salary;
                $housing = (float) $emp->housing_allowance;
                $transport = (float) $emp->transportation_allowance;
                $otherAllow = (float) $emp->other_allowance;
                $totalAllowances = $housing + $transport + $otherAllow;

                // Overtime calculation
                $hourlyRate = $basic > 0 ? ($basic / 30 / 8) : 0;
                $approvedOvertimeMinutes = (int) OvertimeRequest::where('employee_id', $emp->id)
                    ->whereBetween('date', [$period->start_date, $period->end_date])
                    ->where('status', 'approved')
                    ->sum('minutes');

                $overtimeAmount = round(($approvedOvertimeMinutes / 60) * $hourlyRate * 1.5, 2);

                // Salary advance deduction
                $advanceDeduction = 0.00;
                $activeAdvance = SalaryAdvance::where('employee_id', $emp->id)
                    ->whereIn('status', ['approved', 'active'])
                    ->where('remaining_amount', '>', 0)
                    ->first();
                if ($activeAdvance) {
                    $advanceDeduction = min((float)$activeAdvance->installment_amount, (float)$activeAdvance->remaining_amount);
                }

                // Loan deduction
                $loanDeduction = 0.00;
                $activeLoan = EmployeeLoan::where('employee_id', $emp->id)
                    ->where('status', 'active')
                    ->where('remaining_balance', '>', 0)
                    ->first();
                if ($activeLoan) {
                    $loanDeduction = min((float)$activeLoan->installment_amount, (float)$activeLoan->remaining_balance);
                }

                // Lateness deduction if late minutes > 120 minutes in the month
                $totalLateMinutes = (int) AttendanceRecord::where('employee_id', $emp->id)
                    ->whereBetween('attendance_date', [$period->start_date, $period->end_date])
                    ->sum('late_minutes');
                $lateDeduction = 0.00;
                if ($totalLateMinutes > 120 && $hourlyRate > 0) {
                    $excessMinutes = $totalLateMinutes - 120;
                    $lateDeduction = round(($excessMinutes / 60) * $hourlyRate, 2);
                }

                $gross = $basic + $totalAllowances + $overtimeAmount;
                $totalDeductions = $advanceDeduction + $loanDeduction + $lateDeduction;
                $net = max(0.00, $gross - $totalDeductions);

                $periodGross += $gross;
                $periodDeductions += $totalDeductions;
                $periodNet += $net;

                // Create or update payroll record
                $record = PayrollRecord::updateOrCreate(
                    [
                        'payroll_period_id' => $period->id,
                        'employee_id' => $emp->id,
                    ],
                    [
                        'basic_salary' => $basic,
                        'total_allowances' => $totalAllowances,
                        'overtime_amount' => $overtimeAmount,
                        'bonus_amount' => 0.00,
                        'gross_salary' => $gross,
                        'total_deductions' => $totalDeductions,
                        'advance_deduction' => $advanceDeduction,
                        'loan_deduction' => $loanDeduction,
                        'net_salary' => $net,
                        'status' => 'calculated',
                    ]
                );

                // Refresh line items
                $record->items()->delete();
                $record->items()->create(['name' => 'Basic Salary', 'type' => 'earning', 'amount' => $basic]);
                if ($housing > 0) {
                    $record->items()->create(['name' => 'Housing Allowance', 'type' => 'earning', 'amount' => $housing]);
                }
                if ($transport > 0) {
                    $record->items()->create(['name' => 'Transportation Allowance', 'type' => 'earning', 'amount' => $transport]);
                }
                if ($otherAllow > 0) {
                    $record->items()->create(['name' => 'Other Allowance', 'type' => 'earning', 'amount' => $otherAllow]);
                }
                if ($overtimeAmount > 0) {
                    $record->items()->create(['name' => "Overtime ({$approvedOvertimeMinutes} mins)", 'type' => 'earning', 'amount' => $overtimeAmount]);
                }
                if ($advanceDeduction > 0) {
                    $record->items()->create(['name' => 'Salary Advance Repayment', 'type' => 'deduction', 'amount' => $advanceDeduction]);
                }
                if ($loanDeduction > 0) {
                    $record->items()->create(['name' => 'Company Loan Installment', 'type' => 'deduction', 'amount' => $loanDeduction]);
                }
                if ($lateDeduction > 0) {
                    $record->items()->create(['name' => "Lateness Deduction ({$totalLateMinutes} mins)", 'type' => 'deduction', 'amount' => $lateDeduction]);
                }

                // Generate or update Payslip
                $payslipNumber = sprintf('PSL-%s-%s', $emp->employee_number, $period->start_date->format('Ym'));
                Payslip::updateOrCreate(
                    [
                        'payroll_record_id' => $record->id,
                        'employee_id' => $emp->id,
                    ],
                    [
                        'payslip_number' => $payslipNumber,
                        'gross_salary' => $gross,
                        'net_salary' => $net,
                        'generated_at' => now(),
                    ]
                );
            }

            $period->update([
                'total_gross' => $periodGross,
                'total_deductions' => $periodDeductions,
                'total_net' => $periodNet,
                'status' => 'pending_approval',
            ]);

            return [
                'employees_processed' => $employees->count(),
                'total_gross' => $periodGross,
                'total_deductions' => $periodDeductions,
                'total_net' => $periodNet,
            ];
        });
    }

    /**
     * Finalize payroll period, lock calculations, and apply loan/advance balances offsets.
     */
    public function finalizePeriod(PayrollPeriod $period, ?int $finalizedByUserId = null): bool
    {
        if ($period->status === 'finalized') {
            return true;
        }

        return DB::transaction(function () use ($period, $finalizedByUserId) {
            $records = $period->records()->get();

            foreach ($records as $record) {
                // Apply advance deduction
                if ($record->advance_deduction > 0) {
                    $advance = SalaryAdvance::where('employee_id', $record->employee_id)
                        ->whereIn('status', ['approved', 'active'])
                        ->where('remaining_amount', '>', 0)
                        ->first();
                    if ($advance) {
                        $advance->remaining_amount = max(0, $advance->remaining_amount - $record->advance_deduction);
                        if ($advance->remaining_amount <= 0) {
                            $advance->status = 'completed';
                        } else {
                            $advance->status = 'active';
                        }
                        $advance->save();
                    }
                }

                // Apply loan deduction
                if ($record->loan_deduction > 0) {
                    $loan = EmployeeLoan::where('employee_id', $record->employee_id)
                        ->where('status', 'active')
                        ->where('remaining_balance', '>', 0)
                        ->first();
                    if ($loan) {
                        $loan->remaining_balance = max(0, $loan->remaining_balance - $record->loan_deduction);
                        if ($loan->remaining_balance <= 0) {
                            $loan->status = 'completed';
                        }
                        $loan->save();
                    }
                }

                $record->update(['status' => 'approved']);
            }

            $period->update([
                'status' => 'finalized',
                'finalized_by' => $finalizedByUserId,
                'finalized_at' => now(),
            ]);

            return true;
        });
    }
}
