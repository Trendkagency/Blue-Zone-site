<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Candidate;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeTraining;
use App\Models\JobVacancy;
use App\Models\LeaveRequest;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\PerformanceReview;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HrReportController extends Controller
{
    public function index(): View
    {
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('employment_status', 'active')->count();
        $probationEmployees = Employee::where('employment_status', 'probation')->count();
        $turnoverRate = $totalEmployees > 0 ? round((Employee::whereIn('employment_status', ['resigned', 'terminated'])->count() / $totalEmployees) * 100, 1) : 0;

        $deptDistribution = Department::has('employees')->withCount('employees')->get();

        return view('admin.hr.reports.index', compact('totalEmployees', 'activeEmployees', 'probationEmployees', 'turnoverRate', 'deptDistribution'));
    }

    public function employees(Request $request): View
    {
        $query = Employee::with(['department', 'position', 'location']);

        if ($deptId = $request->input('department_id')) {
            $query->where('department_id', $deptId);
        }

        if ($status = $request->input('status')) {
            $query->where('employment_status', $status);
        }

        $employees = $query->paginate(25)->withQueryString();
        $departments = Department::where('is_active', true)->get();

        return view('admin.hr.reports.employees', compact('employees', 'departments'));
    }

    public function attendance(Request $request): View
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $records = AttendanceRecord::with(['employee.department'])
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->latest('attendance_date')
            ->paginate(25)
            ->withQueryString();

        $stats = [
            'present' => AttendanceRecord::whereBetween('attendance_date', [$startDate, $endDate])->where('status', 'present')->count(),
            'late' => AttendanceRecord::whereBetween('attendance_date', [$startDate, $endDate])->where('status', 'late')->count(),
            'absent' => AttendanceRecord::whereBetween('attendance_date', [$startDate, $endDate])->where('status', 'absent')->count(),
        ];

        return view('admin.hr.reports.attendance', compact('records', 'startDate', 'endDate', 'stats'));
    }

    public function leave(Request $request): View
    {
        $requests = LeaveRequest::with(['employee.department', 'leaveType'])
            ->where('status', 'approved')
            ->latest()
            ->paginate(25);

        return view('admin.hr.reports.leave', compact('requests'));
    }

    public function payroll(Request $request): View
    {
        $periods = PayrollPeriod::with('records.employee')->latest('start_date')->take(12)->get();
        return view('admin.hr.reports.payroll', compact('periods'));
    }

    public function recruitment(): View
    {
        $vacancies = JobVacancy::withCount('candidates')->latest()->get();
        $candidates = Candidate::with('vacancy')->latest()->take(20)->get();

        return view('admin.hr.reports.recruitment', compact('vacancies', 'candidates'));
    }

    public function performance(): View
    {
        $reviews = PerformanceReview::with(['employee.department', 'reviewer', 'cycle'])->latest()->paginate(25);
        return view('admin.hr.reports.performance', compact('reviews'));
    }

    public function training(): View
    {
        $trainings = EmployeeTraining::with(['employee.department', 'program'])->latest()->paginate(25);
        return view('admin.hr.reports.training', compact('trainings'));
    }
}
