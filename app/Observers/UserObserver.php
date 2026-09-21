<?php

namespace App\Observers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Services\Hr\EmployeeService;
use Illuminate\Support\Facades\Log;

/**
 * Observer for User Model.
 * Implements the Observer design pattern to monitor user lifecycle
 * and automatically synchronize employee records in the HR module.
 */
class UserObserver
{
    public function created(User $user): void
    {
        $this->syncEmployeeProfile($user);
    }

    public function updated(User $user): void
    {
        $this->syncEmployeeProfile($user);
    }

    public function deleted(User $user): void
    {
        $employee = Employee::where('user_id', $user->id)->first();
        if ($employee) {
            $employee->update(['employment_status' => 'terminated']);
            $employee->delete();
            Log::info("UserObserver: Terminated and soft-deleted employee #{$employee->id} for user #{$user->id}");
        }
    }

    public function restored(User $user): void
    {
        $employee = Employee::withTrashed()->where('user_id', $user->id)->first();
        if ($employee) {
            $employee->restore();
            $employee->update(['employment_status' => 'active']);
            Log::info("UserObserver: Restored employee #{$employee->id} for user #{$user->id}");
        }
    }

    /**
     * Synchronize or create employee profile corresponding to a system user.
     */
    public function syncEmployeeProfile(User $user): ?Employee
    {
        try {
            $employee = Employee::withTrashed()
                ->where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();

            // Parse names (handling honorifics & designations)
            $cleanName = trim(preg_replace('/^(Dr\.|Mr\.|Mrs\.|Ms\.)\s+/i', '', $user->name));
            $cleanName = trim(preg_replace('/\s*\([^)]*\)/', '', $cleanName));
            $parts = explode(' ', $cleanName, 2);
            $firstName = $parts[0] ?? $user->name;
            $lastName = $parts[1] ?? '';

            // Map Department and Position according to user role
            $roleName = strtolower(str_replace([' ', '-', '_'], '', $user->role?->name ?? ''));

            $deptCode = 'DEP-MGMT';
            $posCode = 'POS-CEO';

            if (str_contains($roleName, 'sale')) {
                $deptCode = 'DEP-SALES';
                $posCode = 'POS-SR';
            } elseif (str_contains($roleName, 'inventory') || str_contains($roleName, 'warehouse') || str_contains($roleName, 'stock')) {
                $deptCode = 'DEP-OPS';
                $posCode = 'POS-OPM';
            } elseif (str_contains($roleName, 'mr') || str_contains($roleName, 'medical') || str_contains($roleName, 'rep')) {
                $deptCode = 'DEP-MED';
                $posCode = 'POS-MR';
            } elseif (str_contains($roleName, 'manager')) {
                $deptCode = 'DEP-HR';
                $posCode = 'POS-HRM';
            } elseif (str_contains($roleName, 'admin') || str_contains($roleName, 'it') || str_contains($roleName, 'tech')) {
                $deptCode = 'DEP-IT';
                $posCode = 'POS-ITS';
            }

            $dept = Department::where('code', $deptCode)->first() ?? Department::first();
            $pos = Position::where('code', $posCode)->first() ?? Position::first();
            $schedule = WorkSchedule::first();

            $status = ($user->status === 'inactive' || $user->status === 'suspended') ? 'suspended' : 'active';

            if ($employee) {
                if ($employee->trashed() && $status === 'active') {
                    $employee->restore();
                }

                $employee->update(array_filter([
                    'user_id' => $user->id,
                    'first_name' => $firstName ?: $employee->first_name,
                    'last_name' => $lastName ?: ($employee->last_name ?: $firstName),
                    'email' => $user->email,
                    'phone' => $user->phone ?: $employee->phone,
                    'country_id' => $user->country_id ?: $employee->country_id,
                    'city_id' => $user->city_id ?: $employee->city_id,
                    'department_id' => $employee->department_id ?: $dept?->id,
                    'position_id' => $employee->position_id ?: $pos?->id,
                    'employment_status' => $status,
                ]));

                return $employee;
            }

            $employeeService = new EmployeeService();
            $salary = match($posCode) {
                'POS-CEO' => 25000.00,
                'POS-HRM', 'POS-OPM', 'POS-SM' => 15000.00,
                'POS-ITS' => 16000.00,
                'POS-MR' => 12000.00,
                'POS-SR' => 9500.00,
                default => 10000.00,
            };

            $employee = $employeeService->createEmployee([
                'user_id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName ?: $firstName,
                'email' => $user->email,
                'phone' => $user->phone ?: ('+9665' . rand(10000000, 99999999)),
                'country_id' => $user->country_id,
                'city_id' => $user->city_id,
                'department_id' => $dept?->id,
                'position_id' => $pos?->id,
                'work_schedule_id' => $schedule?->id,
                'employment_type' => 'full_time',
                'employment_status' => $status,
                'hire_date' => $user->created_at?->toDateString() ?? now()->toDateString(),
                'contract_start_date' => $user->created_at?->toDateString() ?? now()->toDateString(),
                'basic_salary' => $salary,
                'housing_allowance' => round($salary * 0.25, 2),
                'transportation_allowance' => round($salary * 0.10, 2),
                'payment_method' => 'bank_transfer',
                'nationality' => 'Saudi Arabia',
            ], $user->id);

            Log::info("UserObserver: Created and synced employee #{$employee->id} [{$employee->employee_number}] for user #{$user->id}");

            return $employee;
        } catch (\Throwable $e) {
            Log::error("UserObserver: Error syncing employee for user #{$user->id}: " . $e->getMessage());
            return null;
        }
    }
}
