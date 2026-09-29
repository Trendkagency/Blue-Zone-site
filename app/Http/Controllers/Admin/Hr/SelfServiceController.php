<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\Hr\AttendanceService;
use App\Services\Hr\LeaveService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SelfServiceController extends Controller
{
    protected AttendanceService $attendanceService;
    protected LeaveService $leaveService;

    public function __construct(AttendanceService $attendanceService, LeaveService $leaveService)
    {
        $this->attendanceService = $attendanceService;
        $this->leaveService = $leaveService;
    }

    /**
     * Get current status for authenticated user (Attendance today, leave balances, recent requests).
     */
    public function status(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $employee = $user->getOrCreateEmployee();
        $today = now()->toDateString();
        $year = (int) now()->year;

        // Today's attendance
        $todayAttendance = AttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        // Active leave types
        $leaveTypes = LeaveType::where('is_active', true)->get()->map(function ($type) use ($employee, $year) {
            $balance = LeaveBalance::where('employee_id', $employee->id)
                ->where('leave_type_id', $type->id)
                ->where('year', $year)
                ->first();

            $remaining = $balance ? (float) $balance->remaining_days : (float) $type->annual_days;
            $allocated = $balance ? (float) $balance->allocated_days : (float) $type->annual_days;
            $used = $balance ? (float) $balance->used_days : 0.0;
            $pending = $balance ? (float) $balance->pending_days : 0.0;

            return [
                'id' => $type->id,
                'name' => app()->getLocale() === 'ar' ? $type->name_ar : $type->name_en,
                'name_en' => $type->name_en,
                'name_ar' => $type->name_ar,
                'code' => $type->code,
                'annual_days' => $type->annual_days,
                'remaining_days' => $remaining,
                'allocated_days' => $allocated,
                'used_days' => $used,
                'pending_days' => $pending,
                'requires_attachment' => (bool) $type->requires_attachment,
            ];
        });

        // Recent leave requests
        $recentLeaves = LeaveRequest::with('leaveType')
            ->where('employee_id', $employee->id)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($lr) {
                return [
                    'id' => $lr->id,
                    'type_name' => app()->getLocale() === 'ar' ? ($lr->leaveType?->name_ar ?? 'إجازة') : ($lr->leaveType?->name_en ?? 'Leave'),
                    'start_date' => $lr->start_date ? Carbon::parse($lr->start_date)->format('Y-m-d') : '',
                    'end_date' => $lr->end_date ? Carbon::parse($lr->end_date)->format('Y-m-d') : '',
                    'total_days' => (float) $lr->total_days,
                    'status' => $lr->status,
                    'reason' => $lr->reason,
                    'rejection_reason' => $lr->rejection_reason,
                    'created_at' => $lr->created_at ? $lr->created_at->diffForHumans() : '',
                ];
            });

        // Shift info if assigned
        $schedule = $employee->workSchedule;
        $shiftInfo = null;
        if ($schedule) {
            $shiftInfo = [
                'name' => $schedule->name,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'grace_minutes' => $schedule->grace_minutes,
            ];
        }

        return response()->json([
            'success' => true,
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->full_name,
                'number' => $employee->employee_number,
                'department' => $employee->department?->name ?? (app()->getLocale() === 'ar' ? 'العام' : 'General'),
                'position' => $employee->position?->name ?? (app()->getLocale() === 'ar' ? 'موظف' : 'Staff'),
            ],
            'attendance' => [
                'date' => $today,
                'has_record' => !is_null($todayAttendance),
                'check_in' => $todayAttendance?->check_in,
                'check_out' => $todayAttendance?->check_out,
                'is_checked_in' => !empty($todayAttendance?->check_in),
                'is_checked_out' => !empty($todayAttendance?->check_out),
                'status' => $todayAttendance?->status ?? 'not_checked_in',
                'worked_minutes' => $todayAttendance?->worked_minutes ?? 0,
                'late_minutes' => $todayAttendance?->late_minutes ?? 0,
                'early_leave_minutes' => $todayAttendance?->early_leave_minutes ?? 0,
            ],
            'shift' => $shiftInfo,
            'leave_types' => $leaveTypes,
            'recent_leaves' => $recentLeaves,
            'server_time' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Self Check In for the authenticated employee.
     */
    public function selfCheckIn(Request $request): JsonResponse|RedirectResponse
    {
        $user = auth()->user();
        if (!$user) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => __('auth.failed')], 401);
            }
            return back()->withErrors(['error' => __('auth.failed')]);
        }

        $employee = $user->getOrCreateEmployee();

        try {
            $time = now()->format('H:i:s');
            $record = $this->attendanceService->recordCheckIn($employee->id, $time, 'web');

            $msg = app()->getLocale() === 'ar'
                ? "تم تسجيل حضورك بنجاح في الساعة {$record->check_in}"
                : "Check-in recorded successfully at {$record->check_in}";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'record' => $record,
                ]);
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            $errMsg = $e->getMessage();
            if (str_contains($errMsg, 'already checked in')) {
                $errMsg = app()->getLocale() === 'ar'
                    ? 'لقد قمت بتسجيل الحضور بالفعل لهذا اليوم.'
                    : 'You have already checked in today.';
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $errMsg], 422);
            }

            return back()->withErrors(['error' => $errMsg]);
        }
    }

    /**
     * Self Check Out for the authenticated employee.
     */
    public function selfCheckOut(Request $request): JsonResponse|RedirectResponse
    {
        $user = auth()->user();
        if (!$user) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => __('auth.failed')], 401);
            }
            return back()->withErrors(['error' => __('auth.failed')]);
        }

        $employee = $user->getOrCreateEmployee();

        try {
            $time = now()->format('H:i:s');
            $record = $this->attendanceService->recordCheckOut($employee->id, $time);

            $workedHours = round($record->worked_minutes / 60, 1);
            $msg = app()->getLocale() === 'ar'
                ? "تم تسجيل الانصراف بنجاح في الساعة {$record->check_out} (إجمالي ساعات العمل: {$workedHours} ساعة)"
                : "Check-out recorded successfully at {$record->check_out} (Total worked: {$workedHours} hrs)";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'record' => $record,
                ]);
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            $errMsg = $e->getMessage();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $errMsg], 422);
            }

            return back()->withErrors(['error' => $errMsg]);
        }
    }

    /**
     * Submit a Leave Request on behalf of the logged-in employee.
     */
    public function submitLeave(Request $request): JsonResponse|RedirectResponse
    {
        $user = auth()->user();
        if (!$user) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => __('auth.failed')], 401);
            }
            return back()->withErrors(['error' => __('auth.failed')]);
        }

        $validated = $request->validate([
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'min:3', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx', 'max:5120'],
        ]);

        $employee = $user->getOrCreateEmployee();

        // Handle attachment upload if present
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave_attachments', 'public');
        }

        $payload = [
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'attachment_path' => $attachmentPath,
        ];

        try {
            $leaveRequest = $this->leaveService->submitRequest($employee, $payload);

            $msg = app()->getLocale() === 'ar'
                ? "تم تقديم طلب الإجازة بنجاح ({$leaveRequest->total_days} يوم) وهو بانتظار الاعتماد."
                : "Leave request submitted successfully ({$leaveRequest->total_days} days) and is pending approval.";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'leave_request' => $leaveRequest->load('leaveType'),
                ]);
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            $errMsg = $e->getMessage();
            if (str_contains($errMsg, 'Insufficient leave balance')) {
                $errMsg = app()->getLocale() === 'ar'
                    ? 'رصيد الإجازات المتاح غير كافٍ لتغطية عدد الأيام المطلوبة.'
                    : 'Insufficient leave balance to cover the requested days.';
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $errMsg], 422);
            }

            return back()->withErrors(['error' => $errMsg]);
        }
    }

    /**
     * Dedicated view for Employee Self Service page (Time Clock & Leave Requests).
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $employee = $user->getOrCreateEmployee();
        $today = now()->toDateString();
        $year = (int) now()->year;

        $todayAttendance = AttendanceRecord::where('employee_id', $employee->id)
            ->where('attendance_date', $today)
            ->first();

        $leaveTypes = LeaveType::where('is_active', true)->get();

        $leaveBalances = LeaveBalance::with('leaveType')
            ->where('employee_id', $employee->id)
            ->where('year', $year)
            ->get();

        $leaveRequests = LeaveRequest::with(['leaveType', 'approver', 'rejecter'])
            ->where('employee_id', $employee->id)
            ->latest()
            ->paginate(10);

        $attendanceHistory = AttendanceRecord::where('employee_id', $employee->id)
            ->latest('attendance_date')
            ->limit(14)
            ->get();

        return view('admin.hr.self-service.index', compact(
            'employee',
            'todayAttendance',
            'leaveTypes',
            'leaveBalances',
            'leaveRequests',
            'attendanceHistory',
            'year'
        ));
    }
}
