<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeePromotion;
use App\Models\PerformanceCycle;
use App\Models\PerformanceGoal;
use App\Models\PerformanceKpi;
use App\Models\PerformanceReview;
use App\Models\Position;
use App\Services\Hr\PerformanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerformanceController extends Controller
{
    protected PerformanceService $performanceService;

    public function __construct(PerformanceService $performanceService)
    {
        $this->performanceService = $performanceService;
    }

    public function reviews(Request $request): View
    {
        $query = PerformanceReview::with(['employee.department', 'employee.position', 'reviewer', 'cycle']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();
        $cycles = PerformanceCycle::all();
        $employees = Employee::where('employment_status', 'active')->get();

        return view('admin.hr.performance.reviews', compact('reviews', 'cycles', 'employees'));
    }

    public function goals(Request $request): View
    {
        $query = PerformanceGoal::with(['employee.department', 'cycle']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $goals = $query->latest()->paginate(15)->withQueryString();
        $cycles = PerformanceCycle::all();
        $employees = Employee::where('employment_status', 'active')->get();

        return view('admin.hr.performance.goals', compact('goals', 'cycles', 'employees'));
    }

    public function storeGoal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'cycle_id' => ['required', 'exists:performance_cycles,id'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'target' => ['nullable', 'string'],
        ]);

        PerformanceGoal::create(array_merge($validated, [
            'status' => 'in_progress',
            'achievement_percentage' => 0,
        ]));

        return back()->with('success', __('hr.goal_created', ['default' => 'Performance goal assigned.']));
    }

    public function kpis(): View
    {
        $kpis = PerformanceKpi::latest()->paginate(15);
        $departments = Department::where('is_active', true)->get();
        return view('admin.hr.performance.kpis', compact('kpis', 'departments'));
    }

    public function promotions(): View
    {
        $promotions = EmployeePromotion::with(['employee', 'oldDepartment', 'newDepartment', 'oldPosition', 'newPosition'])
            ->latest('effective_date')
            ->paginate(15);
        $employees = Employee::where('employment_status', 'active')->get();
        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();

        return view('admin.hr.performance.promotions', compact('promotions', 'employees', 'departments', 'positions'));
    }

    public function storePromotion(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'new_department_id' => ['required', 'exists:departments,id'],
            'new_position_id' => ['required', 'exists:positions,id'],
            'new_salary' => ['required', 'numeric', 'min:0'],
            'effective_date' => ['required', 'date'],
            'reason' => ['required', 'string'],
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        try {
            $this->performanceService->executePromotion($employee, $validated, auth()->id());
            return back()->with('success', __('hr.promotion_recorded', ['default' => 'Promotion applied and employee profile updated.']));
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
