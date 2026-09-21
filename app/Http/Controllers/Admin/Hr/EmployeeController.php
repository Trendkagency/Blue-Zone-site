<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Services\Hr\EmployeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    protected EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index(Request $request): View
    {
        $query = Employee::with(['department', 'position', 'location', 'user']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('employee_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($deptId = $request->input('department_id')) {
            $query->where('department_id', $deptId);
        }

        if ($posId = $request->input('position_id')) {
            $query->where('position_id', $posId);
        }

        if ($status = $request->input('status')) {
            $query->where('employment_status', $status);
        }

        if ($type = $request->input('employment_type')) {
            $query->where('employment_type', $type);
        }

        if ($locId = $request->input('location_id')) {
            $query->where('location_id', $locId);
        }

        $employees = $query->latest('id')->paginate(15)->withQueryString();

        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();
        $locations = Location::where('is_active', true)->get();

        return view('admin.hr.employees.index', compact('employees', 'departments', 'positions', 'locations'));
    }

    public function create(): View
    {
        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();
        $locations = Location::where('is_active', true)->get();
        $workSchedules = WorkSchedule::where('is_active', true)->get();
        $countries = Country::where('is_active', true)->get();
        $cities = City::where('is_active', true)->get();
        $managers = Employee::where('employment_status', 'active')->get();
        $availableUsers = User::doesntHave('employee')->where('status', 'active')->get();

        return view('admin.hr.employees.create', compact(
            'departments',
            'positions',
            'locations',
            'workSchedules',
            'countries',
            'cities',
            'managers',
            'availableUsers'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'first_name_ar' => ['nullable', 'string', 'max:100'],
            'last_name_ar' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'alternate_phone' => ['nullable', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string'],
            'marital_status' => ['nullable', 'string'],
            'nationality' => ['nullable', 'string'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'manager_employee_id' => ['nullable', 'exists:employees,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'employment_type' => ['required', 'string'],
            'employment_status' => ['required', 'string'],
            'hire_date' => ['required', 'date'],
            'contract_start_date' => ['nullable', 'date'],
            'contract_end_date' => ['nullable', 'date', 'after_or_equal:contract_start_date'],
            'work_schedule_id' => ['nullable', 'exists:work_schedules,id'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'housing_allowance' => ['nullable', 'numeric', 'min:0'],
            'transportation_allowance' => ['nullable', 'numeric', 'min:0'],
            'other_allowance' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string'],
            'bank_account_number' => ['nullable', 'string'],
            'iban' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $employee = $this->employeeService->createEmployee($validated, auth()->id());

        return redirect()->route('admin.hr.employees.show', $employee->id)
            ->with('success', __('hr.employee_created_successfully', ['default' => 'Employee profile successfully created.']));
    }

    public function show(int $id): View
    {
        $employee = Employee::with([
            'department',
            'position',
            'location',
            'user',
            'manager',
            'workSchedule',
            'documents',
            'contracts',
            'salaryHistory',
            'attendanceRecords' => fn ($q) => $q->latest('attendance_date')->take(10),
            'leaveRequests' => fn ($q) => $q->with('leaveType')->latest()->take(10),
            'leaveBalances.leaveType',
            'payrollRecords' => fn ($q) => $q->with('period')->latest()->take(6),
            'performanceReviews.cycle',
            'trainingRecords.program',
            'assetAssignments.asset',
            'requests' => fn ($q) => $q->latest()->take(5),
            'disciplinaryActions' => fn ($q) => $q->latest()->take(5),
            'promotions' => fn ($q) => $q->with(['oldPosition', 'newPosition', 'oldDepartment', 'newDepartment'])->latest(),
            'offboarding',
        ])->findOrFail($id);

        $timeline = $this->employeeService->getTimeline($employee);

        return view('admin.hr.employees.show', compact('employee', 'timeline'));
    }

    public function edit(int $id): View
    {
        $employee = Employee::findOrFail($id);
        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();
        $locations = Location::where('is_active', true)->get();
        $workSchedules = WorkSchedule::where('is_active', true)->get();
        $countries = Country::where('is_active', true)->get();
        $cities = City::where('is_active', true)->get();
        $managers = Employee::where('employment_status', 'active')->where('id', '!=', $employee->id)->get();
        $availableUsers = User::where(function ($q) use ($employee) {
            $q->doesntHave('employee')->orWhere('id', $employee->user_id);
        })->where('status', 'active')->get();

        return view('admin.hr.employees.edit', compact(
            'employee',
            'departments',
            'positions',
            'locations',
            'workSchedules',
            'countries',
            'cities',
            'managers',
            'availableUsers'
        ));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'first_name_ar' => ['nullable', 'string', 'max:100'],
            'last_name_ar' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'alternate_phone' => ['nullable', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string'],
            'marital_status' => ['nullable', 'string'],
            'nationality' => ['nullable', 'string'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'manager_employee_id' => ['nullable', 'exists:employees,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'employment_type' => ['required', 'string'],
            'employment_status' => ['required', 'string'],
            'hire_date' => ['required', 'date'],
            'probation_start_date' => ['nullable', 'date'],
            'probation_end_date' => ['nullable', 'date'],
            'contract_start_date' => ['nullable', 'date'],
            'contract_end_date' => ['nullable', 'date'],
            'work_schedule_id' => ['nullable', 'exists:work_schedules,id'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'housing_allowance' => ['nullable', 'numeric', 'min:0'],
            'transportation_allowance' => ['nullable', 'numeric', 'min:0'],
            'other_allowance' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string'],
            'bank_account_number' => ['nullable', 'string'],
            'iban' => ['nullable', 'string'],
            'salary_change_reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->employeeService->updateEmployee($employee, $validated, auth()->id());

        return redirect()->route('admin.hr.employees.show', $employee->id)
            ->with('success', __('hr.employee_updated_successfully', ['default' => 'Employee profile updated successfully.']));
    }

    public function destroy(int $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return redirect()->route('admin.hr.employees.index')
            ->with('success', __('hr.employee_archived_successfully', ['default' => 'Employee safely archived.']));
    }
}
