<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeDisciplinaryAction;
use App\Models\EmployeeRequest;
use App\Models\EmployeeTransfer;
use App\Models\Location;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeRequestController extends Controller
{
    public function index(Request $request): View
    {
        $query = EmployeeRequest::with(['employee.department', 'approver']);

        if ($type = $request->input('request_type')) {
            $query->where('request_type', $type);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();
        $employees = Employee::where('employment_status', 'active')->get();

        return view('admin.hr.requests.index', compact('requests', 'employees'));
    }

    public function complaints(): View
    {
        $complaints = EmployeeRequest::with(['employee.department', 'approver'])
            ->where('request_type', 'complaint')
            ->latest()
            ->paginate(15);
        $employees = Employee::where('employment_status', 'active')->get();

        return view('admin.hr.requests.complaints', compact('complaints', 'employees'));
    }

    public function suggestions(): View
    {
        $suggestions = EmployeeRequest::with(['employee.department'])
            ->where('request_type', 'suggestion')
            ->latest()
            ->paginate(15);
        $employees = Employee::where('employment_status', 'active')->get();

        return view('admin.hr.requests.suggestions', compact('suggestions', 'employees'));
    }

    public function storeRequest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'request_type' => ['required', 'string'],
            'subject' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
        ]);

        EmployeeRequest::create(array_merge($validated, [
            'status' => 'pending',
        ]));

        return back()->with('success', __('hr.request_submitted', ['default' => 'Request registered successfully.']));
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $empRequest = EmployeeRequest::findOrFail($id);
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected,in_progress,completed'],
            'admin_response' => ['nullable', 'string'],
        ]);

        $empRequest->update([
            'status' => $validated['status'],
            'admin_response' => $validated['admin_response'] ?? null,
            'approved_by' => in_array($validated['status'], ['approved', 'completed']) ? auth()->id() : null,
        ]);

        return back()->with('success', __('hr.request_status_updated', ['default' => 'Request status updated.']));
    }

    public function transfers(): View
    {
        $transfers = EmployeeTransfer::with(['employee', 'oldDepartment', 'newDepartment', 'oldPosition', 'newPosition', 'oldLocation', 'newLocation'])
            ->latest('effective_date')
            ->paginate(15);
        $employees = Employee::where('employment_status', 'active')->get();
        $departments = Department::where('is_active', true)->get();
        $positions = Position::where('is_active', true)->get();
        $locations = Location::where('is_active', true)->get();

        return view('admin.hr.requests.transfers', compact('transfers', 'employees', 'departments', 'positions', 'locations'));
    }

    public function storeTransfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'new_department_id' => ['nullable', 'exists:departments,id'],
            'new_position_id' => ['nullable', 'exists:positions,id'],
            'new_location_id' => ['nullable', 'exists:locations,id'],
            'effective_date' => ['required', 'date'],
            'reason' => ['required', 'string'],
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        EmployeeTransfer::create([
            'employee_id' => $employee->id,
            'old_department_id' => $employee->department_id,
            'new_department_id' => $validated['new_department_id'] ?? $employee->department_id,
            'old_position_id' => $employee->position_id,
            'new_position_id' => $validated['new_position_id'] ?? $employee->position_id,
            'old_location_id' => $employee->location_id,
            'new_location_id' => $validated['new_location_id'] ?? $employee->location_id,
            'effective_date' => $validated['effective_date'],
            'reason' => $validated['reason'],
            'approved_by' => auth()->id(),
        ]);

        $employee->update([
            'department_id' => $validated['new_department_id'] ?? $employee->department_id,
            'position_id' => $validated['new_position_id'] ?? $employee->position_id,
            'location_id' => $validated['new_location_id'] ?? $employee->location_id,
        ]);

        return back()->with('success', __('hr.transfer_executed', ['default' => 'Transfer recorded and employee updated.']));
    }

    public function disciplinary(): View
    {
        $actions = EmployeeDisciplinaryAction::with(['employee.department', 'approver'])
            ->latest('incident_date')
            ->paginate(15);
        $employees = Employee::where('employment_status', 'active')->get();

        return view('admin.hr.requests.disciplinary', compact('actions', 'employees'));
    }

    public function storeDisciplinary(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'incident_date' => ['required', 'date'],
            'incident_type' => ['required', 'string'],
            'description' => ['required', 'string'],
            'action_type' => ['required', 'string'],
            'action_date' => ['required', 'date'],
        ]);

        EmployeeDisciplinaryAction::create(array_merge($validated, [
            'status' => 'active',
            'approved_by' => auth()->id(),
        ]));

        return back()->with('success', __('hr.disciplinary_recorded', ['default' => 'Disciplinary action recorded.']));
    }
}
