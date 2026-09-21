<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Employee;
use App\Models\OvertimeRequest;
use App\Services\Hr\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request): View
    {
        $query = AttendanceRecord::with(['employee.department', 'employee.position']);

        if ($search = $request->input('search')) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        if ($date = $request->input('date')) {
            $query->where('attendance_date', $date);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($deptId = $request->input('department_id')) {
            $query->whereHas('employee', function ($q) use ($deptId) {
                $q->where('department_id', $deptId);
            });
        }

        $records = $query->latest('attendance_date')->latest('check_in')->paginate(20)->withQueryString();
        $departments = Department::where('is_active', true)->get();
        $employees = Employee::where('employment_status', 'active')->get();

        return view('admin.hr.attendance.index', compact('records', 'departments', 'employees'));
    }

    public function daily(Request $request): View
    {
        $targetDate = $request->input('date', now()->toDateString());
        $activeEmployees = Employee::with(['department', 'position', 'workSchedule'])
            ->where('employment_status', 'active')
            ->get();

        $records = AttendanceRecord::where('attendance_date', $targetDate)
            ->get()
            ->keyBy('employee_id');

        $presentCount = $records->whereIn('status', ['present', 'late'])->count();
        $lateCount = $records->where('status', 'late')->count();
        $absentCount = max(0, $activeEmployees->count() - $presentCount);

        return view('admin.hr.attendance.daily', compact('activeEmployees', 'records', 'targetDate', 'presentCount', 'lateCount', 'absentCount'));
    }

    public function checkIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'check_in' => ['nullable', 'date_format:H:i'],
            'attendance_date' => ['nullable', 'date'],
        ]);

        try {
            $this->attendanceService->recordCheckIn(
                $validated['employee_id'],
                $validated['check_in'] ?? null,
                'manual',
                $validated['attendance_date'] ?? null
            );
            return back()->with('success', __('hr.check_in_recorded', ['default' => 'Check-in recorded successfully.']));
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function checkOut(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'check_out' => ['nullable', 'date_format:H:i'],
            'attendance_date' => ['nullable', 'date'],
        ]);

        try {
            $this->attendanceService->recordCheckOut(
                $validated['employee_id'],
                $validated['check_out'] ?? null,
                $validated['attendance_date'] ?? null
            );
            return back()->with('success', __('hr.check_out_recorded', ['default' => 'Check-out recorded successfully.']));
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function overtime(Request $request): View
    {
        $query = OvertimeRequest::with('employee');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $overtimeRequests = $query->latest('date')->paginate(15)->withQueryString();
        $employees = Employee::where('employment_status', 'active')->get();

        return view('admin.hr.attendance.overtime', compact('overtimeRequests', 'employees'));
    }

    public function storeOvertime(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'minutes' => ['required', 'integer', 'min:1'],
            'reason' => ['nullable', 'string'],
        ]);

        OvertimeRequest::create(array_merge($validated, [
            'status' => 'pending',
            'requested_by' => auth()->id(),
        ]));

        return back()->with('success', __('hr.overtime_requested', ['default' => 'Overtime request submitted.']));
    }

    public function approveOvertime(Request $request, int $id): RedirectResponse
    {
        $ot = OvertimeRequest::findOrFail($id);
        $ot->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', __('hr.overtime_approved', ['default' => 'Overtime request approved.']));
    }
}
