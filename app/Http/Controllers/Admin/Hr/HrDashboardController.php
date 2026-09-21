<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Candidate;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\EmployeeDocument;
use App\Models\EmployeeOffboarding;
use App\Models\EmployeeRequest;
use App\Models\JobVacancy;
use App\Models\LeaveRequest;
use App\Models\Location;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\PerformanceReview;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HrDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $today = now()->toDateString();

        // Metric counts
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('employment_status', 'active')->count();
        $probationEmployees = Employee::where('employment_status', 'probation')->count();
        $onLeaveEmployees = Employee::where('employment_status', 'on_leave')->count();
        $resignedEmployees = Employee::where('employment_status', 'resigned')->count();
        $terminatedEmployees = Employee::where('employment_status', 'terminated')->count();

        // New hires in past 30 days
        $newHires = Employee::where('hire_date', '>=', now()->subDays(30)->toDateString())->count();

        // Today's attendance
        $todayAttendance = AttendanceRecord::where('attendance_date', $today)->get();
        $presentToday = $todayAttendance->whereIn('status', ['present', 'late'])->count();
        $lateToday = $todayAttendance->where('status', 'late')->count();
        $absentToday = max(0, $activeEmployees - $presentToday);

        // Pending queues
        $pendingLeaves = LeaveRequest::where('status', 'pending')->count();
        $pendingRequests = EmployeeRequest::where('status', 'pending')->count();
        $pendingPayroll = PayrollPeriod::whereIn('status', ['draft', 'processing', 'pending_approval'])->count();
        $upcomingReviews = PerformanceReview::whereIn('status', ['draft', 'submitted'])->count();

        // Expirations
        $contractsExpiringSoon = EmployeeContract::where('status', 'active')
            ->whereDate('end_date', '<=', now()->addDays(30)->toDateString())
            ->whereDate('end_date', '>=', $today)
            ->count();

        $documentsExpiringSoon = EmployeeDocument::where('status', 'valid')
            ->whereDate('expiry_date', '<=', now()->addDays(30)->toDateString())
            ->whereDate('expiry_date', '>=', $today)
            ->count();

        // Chart 1: Employees by Department
        $departmentsChart = Department::has('employees')
            ->withCount('employees')
            ->get()
            ->map(fn ($d) => [
                'name' => $d->name,
                'count' => $d->employees_count,
            ]);

        // Chart 2: Employees by Status
        $statusBreakdown = [
            ['status' => 'Active', 'count' => $activeEmployees, 'color' => '#10B981'],
            ['status' => 'Probation', 'count' => $probationEmployees, 'color' => '#F59E0B'],
            ['status' => 'On Leave', 'count' => $onLeaveEmployees, 'color' => '#38BDF8'],
            ['status' => 'Resigned', 'count' => $resignedEmployees, 'color' => '#94A3B8'],
            ['status' => 'Terminated', 'count' => $terminatedEmployees, 'color' => '#EF4444'],
        ];

        // Chart 3: Recent Payroll Summary
        $recentPayroll = PayrollPeriod::latest('start_date')->take(6)->get()->reverse()->values();

        // Recent Leave Requests
        $recentLeaves = LeaveRequest::with(['employee', 'leaveType'])
            ->latest()
            ->take(5)
            ->get();

        // Recent Employees & Candidates
        $recentEmployees = Employee::with(['department', 'position'])
            ->latest()
            ->take(5)
            ->get();

        $recentCandidates = Candidate::with('vacancy')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.hr.dashboard', compact(
            'totalEmployees',
            'activeEmployees',
            'probationEmployees',
            'onLeaveEmployees',
            'resignedEmployees',
            'terminatedEmployees',
            'newHires',
            'presentToday',
            'lateToday',
            'absentToday',
            'pendingLeaves',
            'pendingRequests',
            'pendingPayroll',
            'upcomingReviews',
            'contractsExpiringSoon',
            'documentsExpiringSoon',
            'departmentsChart',
            'statusBreakdown',
            'recentPayroll',
            'recentLeaves',
            'recentCandidates',
            'recentEmployees'
        ));
    }
}
