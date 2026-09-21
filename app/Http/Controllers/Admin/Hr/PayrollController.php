<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\Payslip;
use App\Models\SalaryAdvance;
use App\Models\SalaryComponent;
use App\Models\SalaryStructure;
use App\Services\Hr\PayrollService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    protected PayrollService $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function index(Request $request): View
    {
        $query = PayrollRecord::with(['employee.department', 'employee.position', 'period']);

        if ($periodId = $request->input('payroll_period_id')) {
            $query->where('payroll_period_id', $periodId);
        }

        if ($search = $request->input('search')) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        $records = $query->latest('id')->paginate(20)->withQueryString();
        $periods = PayrollPeriod::latest('start_date')->get();

        return view('admin.hr.payroll.index', compact('records', 'periods'));
    }

    public function periods(): View
    {
        $periods = PayrollPeriod::withCount('records')->latest('start_date')->paginate(15);
        return view('admin.hr.payroll.periods', compact('periods'));
    }

    public function storePeriod(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'payment_date' => ['nullable', 'date'],
        ]);

        PayrollPeriod::create(array_merge($validated, [
            'status' => 'draft',
        ]));

        return back()->with('success', __('hr.payroll_period_created', ['default' => 'Payroll period initialized.']));
    }

    public function generate(int $periodId): RedirectResponse
    {
        $period = PayrollPeriod::findOrFail($periodId);

        try {
            $this->payrollService->generatePeriodPayroll($period);
            return back()->with('success', __('hr.payroll_calculated', ['default' => 'Payroll computed and records generated successfully.']));
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function finalize(int $periodId): RedirectResponse
    {
        $period = PayrollPeriod::findOrFail($periodId);

        try {
            $this->payrollService->finalizePeriod($period, auth()->id());
            return back()->with('success', __('hr.payroll_finalized', ['default' => 'Payroll finalized and locked. Payslips are ready.']));
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function payslips(Request $request): View
    {
        $query = Payslip::with(['employee.department', 'employee.position', 'period']);

        if ($periodId = $request->input('payroll_period_id')) {
            $query->where('payroll_period_id', $periodId);
        }

        $payslips = $query->latest('id')->paginate(20)->withQueryString();
        $periods = PayrollPeriod::latest('start_date')->get();

        return view('admin.hr.payroll.payslips', compact('payslips', 'periods'));
    }

    public function showPayslip(int $id): View
    {
        $payslip = Payslip::with(['employee.department', 'employee.position', 'period'])->findOrFail($id);
        return view('admin.hr.payroll.payslip_show', compact('payslip'));
    }

    public function structures(): View
    {
        $structures = SalaryStructure::withCount('components')->get();
        $components = SalaryComponent::all();
        return view('admin.hr.payroll.structures', compact('structures', 'components'));
    }

    public function advances(Request $request): View
    {
        $advances = SalaryAdvance::with('employee.department')->latest()->paginate(15);
        $employees = Employee::where('employment_status', 'active')->get();
        return view('admin.hr.payroll.advances', compact('advances', 'employees'));
    }

    public function storeAdvance(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'installments' => ['required', 'integer', 'min:1'],
            'request_date' => ['required', 'date'],
            'repayment_start_date' => ['required', 'date', 'after_or_equal:request_date'],
            'reason' => ['nullable', 'string'],
        ]);

        $validated['installment_amount'] = round($validated['amount'] / $validated['installments'], 2);
        $validated['remaining_amount'] = $validated['amount'];
        $validated['status'] = 'approved';
        $validated['approved_date'] = now()->toDateString();

        SalaryAdvance::create($validated);

        return back()->with('success', __('hr.advance_approved', ['default' => 'Salary advance approved and scheduled.']));
    }

    public function loans(Request $request): View
    {
        $loans = EmployeeLoan::with('employee.department')->latest()->paginate(15);
        $employees = Employee::where('employment_status', 'active')->get();
        return view('admin.hr.payroll.loans', compact('loans', 'employees'));
    }

    public function storeLoan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'installments' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['installment_amount'] = round($validated['amount'] / $validated['installments'], 2);
        $validated['remaining_balance'] = $validated['amount'];
        $validated['status'] = 'approved';

        EmployeeLoan::create($validated);

        return back()->with('success', __('hr.loan_approved', ['default' => 'Employee loan granted and active.']));
    }
}
