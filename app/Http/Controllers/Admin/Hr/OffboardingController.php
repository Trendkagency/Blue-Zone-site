<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeOffboarding;
use App\Models\ExitInterview;
use App\Models\FinalSettlement;
use App\Services\Hr\OffboardingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OffboardingController extends Controller
{
    protected OffboardingService $offboardingService;

    public function __construct(OffboardingService $offboardingService)
    {
        $this->offboardingService = $offboardingService;
    }

    public function resignations(): View
    {
        $resignations = EmployeeOffboarding::with(['employee.department'])
            ->where('offboarding_type', 'resignation')
            ->latest('submission_date')
            ->paginate(15);
        $employees = Employee::where('employment_status', 'active')->get();

        return view('admin.hr.offboarding.resignations', compact('resignations', 'employees'));
    }

    public function storeResignation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'submission_date' => ['required', 'date'],
            'notice_period_days' => ['required', 'integer', 'min:0'],
            'last_working_date' => ['nullable', 'date'],
            'reason' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        try {
            $this->offboardingService->submitResignation($employee, $validated);
            return back()->with('success', __('hr.resignation_submitted', ['default' => 'Resignation registered and notice period started.']));
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function terminations(): View
    {
        $terminations = EmployeeOffboarding::with(['employee.department'])
            ->where('offboarding_type', 'termination')
            ->latest('submission_date')
            ->paginate(15);
        $employees = Employee::whereIn('employment_status', ['active', 'probation'])->get();

        return view('admin.hr.offboarding.terminations', compact('terminations', 'employees'));
    }

    public function storeTermination(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'last_working_date' => ['required', 'date'],
            'reason' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        try {
            $this->offboardingService->processTermination($employee, $validated, auth()->id());
            return back()->with('success', __('hr.termination_processed', ['default' => 'Termination recorded and offboarding initiated.']));
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function exitInterviews(): View
    {
        $interviews = ExitInterview::with(['employee.department', 'interviewer'])->latest()->paginate(15);
        $offboardings = EmployeeOffboarding::with('employee')->whereIn('status', ['submitted', 'approved'])->get();
        $interviewers = Employee::where('employment_status', 'active')->get();

        return view('admin.hr.offboarding.exit_interviews', compact('interviews', 'offboardings', 'interviewers'));
    }

    public function storeExitInterview(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'interviewer_employee_id' => ['nullable', 'exists:employees,id'],
            'interview_date' => ['required', 'date'],
            'reason_for_leaving' => ['required', 'string'],
            'employee_feedback' => ['nullable', 'string'],
            'management_feedback' => ['nullable', 'string'],
            'workplace_feedback' => ['nullable', 'string'],
            'compensation_feedback' => ['nullable', 'string'],
            'suggestions' => ['nullable', 'string'],
        ]);

        $offboarding = EmployeeOffboarding::where('employee_id', $validated['employee_id'])->first();

        ExitInterview::create(array_merge($validated, [
            'offboarding_id' => $offboarding?->id,
        ]));

        return back()->with('success', __('hr.exit_interview_recorded', ['default' => 'Exit interview details saved.']));
    }

    public function settlements(): View
    {
        $settlements = FinalSettlement::with(['employee.department'])->latest()->paginate(15);
        $offboardings = EmployeeOffboarding::with('employee')->whereIn('status', ['submitted', 'approved'])->get();

        return view('admin.hr.offboarding.settlements', compact('settlements', 'offboardings'));
    }

    public function calculateSettlement(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'offboarding_id' => ['required', 'exists:employee_offboardings,id'],
        ]);

        $offboarding = EmployeeOffboarding::findOrFail($validated['offboarding_id']);

        try {
            $this->offboardingService->calculateFinalSettlement($offboarding);
            return back()->with('success', __('hr.settlement_calculated', ['default' => 'Final settlement calculated successfully.']));
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
