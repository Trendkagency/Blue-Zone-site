<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeTraining;
use App\Models\TrainingCertificate;
use App\Models\TrainingProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainingController extends Controller
{
    public function programs(): View
    {
        $programs = TrainingProgram::withCount('employeeTrainings')->latest()->paginate(15);
        return view('admin.hr.training.programs', compact('programs'));
    }

    public function storeProgram(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'provider' => ['nullable', 'string', 'max:150'],
            'trainer' => ['nullable', 'string', 'max:150'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'duration' => ['nullable', 'string'],
        ]);

        TrainingProgram::create(array_merge($validated, ['status' => 'active']));

        return back()->with('success', __('hr.training_program_created', ['default' => 'Training program created successfully.']));
    }

    public function employeeTraining(Request $request): View
    {
        $trainings = EmployeeTraining::with(['employee.department', 'program'])->latest()->paginate(15);
        $employees = Employee::where('employment_status', 'active')->get();
        $programs = TrainingProgram::all();

        return view('admin.hr.training.employee_training', compact('trainings', 'employees', 'programs'));
    }

    public function assignEmployee(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'training_program_id' => ['required', 'exists:training_programs,id'],
        ]);

        EmployeeTraining::create([
            'employee_id' => $validated['employee_id'],
            'training_program_id' => $validated['training_program_id'],
            'assigned_at' => now(),
            'status' => 'assigned',
        ]);

        return back()->with('success', __('hr.employee_assigned_training', ['default' => 'Employee assigned to training.']));
    }

    public function certificates(): View
    {
        $certificates = TrainingCertificate::with(['employee.department', 'program'])->latest()->paginate(15);
        return view('admin.hr.training.certificates', compact('certificates'));
    }
}
