<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\Hr\LeaveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveController extends Controller
{
    protected LeaveService $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function requests(Request $request): View
    {
        $query = LeaveRequest::with(['employee.department', 'leaveType', 'approver']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($typeId = $request->input('leave_type_id')) {
            $query->where('leave_type_id', $typeId);
        }

        if ($search = $request->input('search')) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        $requests = $query->latest()->paginate(15)->withQueryString();
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $employees = Employee::where('employment_status', 'active')->get();

        return view('admin.hr.leave.requests', compact('requests', 'leaveTypes', 'employees'));
    }

    public function storeRequest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string'],
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        try {
            $this->leaveService->submitRequest($employee, $validated);
            return back()->with('success', __('hr.leave_request_submitted', ['default' => 'Leave request submitted successfully.']));
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function approvals(): View
    {
        $pendingRequests = LeaveRequest::with(['employee.department', 'employee.position', 'leaveType'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('admin.hr.leave.approvals', compact('pendingRequests'));
    }

    public function approveRequest(Request $request, int $id): RedirectResponse
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        try {
            $this->leaveService->approveRequest($leaveRequest, auth()->id());
            return back()->with('success', __('hr.leave_request_approved', ['default' => 'Leave request successfully approved.']));
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function rejectRequest(Request $request, int $id): RedirectResponse
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $reason = $request->input('rejection_reason', 'Rejected by administrator.');

        try {
            $this->leaveService->rejectRequest($leaveRequest, auth()->id(), $reason);
            return back()->with('success', __('hr.leave_request_rejected', ['default' => 'Leave request rejected.']));
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function types(): View
    {
        $types = LeaveType::withCount('requests')->latest()->paginate(15);
        return view('admin.hr.leave.types', compact('types'));
    }

    public function storeType(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_en' => ['required', 'string', 'max:150'],
            'name_ar' => ['required', 'string', 'max:150'],
            'annual_days' => ['required', 'numeric', 'min:0'],
            'is_paid' => ['boolean'],
            'requires_approval' => ['boolean'],
            'requires_attachment' => ['boolean'],
            'carry_forward' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        LeaveType::create($validated);

        return back()->with('success', __('hr.leave_type_created', ['default' => 'Leave type created successfully.']));
    }

    public function balances(Request $request): View
    {
        $year = (int) $request->input('year', now()->year);
        $balances = LeaveBalance::with(['employee.department', 'leaveType'])
            ->where('year', $year)
            ->paginate(20)
            ->withQueryString();

        return view('admin.hr.leave.balances', compact('balances', 'year'));
    }
}
