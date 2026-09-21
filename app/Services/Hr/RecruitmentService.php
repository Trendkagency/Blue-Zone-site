<?php

namespace App\Services\Hr;

use App\Models\Candidate;
use App\Models\Employee;
use App\Models\EmployeeOnboarding;
use App\Models\EmployeeOnboardingTask;
use App\Models\OnboardingTemplate;
use Illuminate\Support\Facades\DB;

class RecruitmentService
{
    protected EmployeeService $employeeService;

    public function __construct(?EmployeeService $employeeService = null)
    {
        $this->employeeService = $employeeService ?? new EmployeeService();
    }

    /**
     * Convert a hired candidate into an official Employee without duplicate records.
     */
    public function convertCandidateToEmployee(Candidate $candidate, array $employeeData, ?int $convertedByUserId = null): Employee
    {
        return DB::transaction(function () use ($candidate, $employeeData, $convertedByUserId) {
            if ($candidate->converted_employee_id) {
                $existing = Employee::find($candidate->converted_employee_id);
                if ($existing) {
                    return $existing;
                }
            }

            // Merge candidate personal details
            $employeeData['first_name'] = $employeeData['first_name'] ?? $candidate->first_name;
            $employeeData['last_name'] = $employeeData['last_name'] ?? $candidate->last_name;
            $employeeData['email'] = $employeeData['email'] ?? $candidate->email;
            $employeeData['phone'] = $employeeData['phone'] ?? $candidate->phone;
            $employeeData['hire_date'] = $employeeData['hire_date'] ?? now()->toDateString();
            $employeeData['employment_status'] = 'probation';
            $employeeData['probation_start_date'] = $employeeData['hire_date'];
            $employeeData['probation_end_date'] = now()->parse($employeeData['hire_date'])->addMonths(3)->toDateString();

            if ($candidate->vacancy) {
                $employeeData['department_id'] = $employeeData['department_id'] ?? $candidate->vacancy->department_id;
                $employeeData['position_id'] = $employeeData['position_id'] ?? $candidate->vacancy->position_id;
                $employeeData['location_id'] = $employeeData['location_id'] ?? $candidate->vacancy->location_id;
            }

            $employee = $this->employeeService->createEmployee($employeeData, $convertedByUserId);

            // Update Candidate record
            $candidate->update([
                'status' => 'hired',
                'converted_employee_id' => $employee->id,
            ]);

            // Assign Onboarding checklist if template exists
            $template = null;
            if (!empty($employeeData['onboarding_template_id'])) {
                $template = OnboardingTemplate::with('tasks')->find($employeeData['onboarding_template_id']);
            } elseif ($employee->department_id) {
                $template = OnboardingTemplate::with('tasks')->where('department_id', $employee->department_id)->where('is_active', true)->first();
            }

            if (!$template) {
                $template = OnboardingTemplate::with('tasks')->where('is_active', true)->first();
            }

            if ($template && $template->tasks->isNotEmpty()) {
                $onboarding = EmployeeOnboarding::create([
                    'employee_id' => $employee->id,
                    'onboarding_template_id' => $template->id,
                    'start_date' => $employee->hire_date,
                    'target_completion_date' => now()->parse($employee->hire_date)->addDays(14)->toDateString(),
                    'status' => 'in_progress',
                ]);

                foreach ($template->tasks as $task) {
                    EmployeeOnboardingTask::create([
                        'employee_onboarding_id' => $onboarding->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'assigned_to_role' => $task->assigned_role,
                        'due_date' => now()->parse($employee->hire_date)->addDays($task->due_days)->toDateString(),
                        'status' => 'pending',
                    ]);
                }
            }

            return $employee;
        });
    }
}
