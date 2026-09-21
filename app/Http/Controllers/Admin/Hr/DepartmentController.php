<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::with(['manager', 'positions'])->withCount('employees')->latest()->paginate(20);
        $managers = Employee::where('employment_status', 'active')->get();

        return view('admin.hr.organization.departments', compact('departments', 'managers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_en' => ['required', 'string', 'max:150'],
            'name_ar' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:30', 'unique:departments,code'],
            'manager_employee_id' => ['nullable', 'exists:employees,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        Department::create($validated);

        return back()->with('success', __('hr.department_created_successfully', ['default' => 'Department created successfully.']));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'name_en' => ['required', 'string', 'max:150'],
            'name_ar' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:30', 'unique:departments,code,' . $department->id],
            'manager_employee_id' => ['nullable', 'exists:employees,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $department->update($validated);

        return back()->with('success', __('hr.department_updated_successfully', ['default' => 'Department updated successfully.']));
    }

    public function destroy(int $id): RedirectResponse
    {
        $department = Department::findOrFail($id);
        if ($department->employees()->count() > 0) {
            return back()->withErrors(['department' => __('hr.cannot_delete_department_with_employees', ['default' => 'Cannot delete department with active employees.'])]);
        }

        $department->delete();

        return back()->with('success', __('hr.department_deleted_successfully', ['default' => 'Department deleted successfully.']));
    }
}
