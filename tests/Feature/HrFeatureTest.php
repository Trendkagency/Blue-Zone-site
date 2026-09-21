<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Department;
use App\Models\Employee;
use App\Models\JobVacancy;
use App\Models\LeaveType;
use App\Models\PayrollPeriod;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $superAdminRole = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            ['name' => 'Super Administrator', 'permissions' => ['*'], 'is_system' => true]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'hr_admin@bluezone.com'],
            ['name' => 'HR Administrator', 'password' => bcrypt('password'), 'role_id' => $superAdminRole->id, 'status' => 'active']
        );
    }

    public function test_admin_can_access_hr_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.hr.dashboard'));
        $response->assertStatus(200);
    }

    public function test_can_create_department_and_position(): void
    {
        $deptResponse = $this->actingAs($this->admin)->post(route('admin.hr.organization.departments.store'), [
            'name_en' => 'Biotech Research',
            'name_ar' => 'أبحاث التكنولوجيا الحيوية',
            'code' => 'BTR',
            'description' => 'Pharmaceutical & Clinical Formulations',
        ]);
        $deptResponse->assertRedirect();
        $this->assertDatabaseHas('departments', ['code' => 'BTR']);

        $department = Department::where('code', 'BTR')->first();

        $posResponse = $this->actingAs($this->admin)->post(route('admin.hr.organization.positions.store'), [
            'department_id' => $department->id,
            'name_en' => 'Lead Formulator',
            'name_ar' => 'كبير أخصائيي التركيبات',
            'code' => 'LFR',
            'level' => 'Senior',
        ]);
        $posResponse->assertRedirect();
        $this->assertDatabaseHas('positions', ['code' => 'LFR']);
    }

    public function test_can_create_employee(): void
    {
        $dept = Department::create(['name_en' => 'Sales', 'name_ar' => 'المبيعات', 'code' => 'SLS']);
        $pos = Position::create(['name_en' => 'Rep', 'name_ar' => 'مندوب', 'code' => 'REP', 'department_id' => $dept->id]);

        $response = $this->actingAs($this->admin)->post(route('admin.hr.employees.store'), [
            'first_name' => 'Tariq',
            'last_name' => 'Al-Mansour',
            'email' => 'tariq.mansour@bluezone.com',
            'phone' => '+966501234567',
            'department_id' => $dept->id,
            'position_id' => $pos->id,
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'hire_date' => now()->toDateString(),
            'basic_salary' => 12500,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('employees', ['email' => 'tariq.mansour@bluezone.com']);
    }

    public function test_can_manage_recruitment_pipeline(): void
    {
        $dept = Department::create(['name_en' => 'Operations', 'name_ar' => 'العمليات', 'code' => 'OPS']);
        $pos = Position::create(['name_en' => 'Supervisor', 'name_ar' => 'مشرف', 'code' => 'SUP', 'department_id' => $dept->id]);

        $vResponse = $this->actingAs($this->admin)->post(route('admin.hr.recruitment.vacancies.store'), [
            'title_en' => 'Warehouse Supervisor',
            'department_id' => $dept->id,
            'position_id' => $pos->id,
            'openings' => 2,
            'employment_type' => 'full_time',
            'salary_min' => 8000,
            'salary_max' => 11000,
            'description' => 'Manage inventory warehouse floor',
            'status' => 'published',
        ]);
        $vResponse->assertRedirect();
        $this->assertDatabaseHas('job_vacancies', ['title_en' => 'Warehouse Supervisor']);

        $vacancy = JobVacancy::where('title_en', 'Warehouse Supervisor')->first();

        $cResponse = $this->actingAs($this->admin)->post(route('admin.hr.recruitment.candidates.store'), [
            'job_vacancy_id' => $vacancy->id,
            'first_name' => 'Kareem',
            'last_name' => 'Fahmy',
            'email' => 'kareem.fahmy@example.com',
            'phone' => '+966551234567',
            'expected_salary' => 9500,
            'source' => 'linkedin',
        ]);
        $cResponse->assertRedirect();
        $this->assertDatabaseHas('candidates', ['email' => 'kareem.fahmy@example.com']);

        $candidate = Candidate::where('email', 'kareem.fahmy@example.com')->first();

        $convertResponse = $this->actingAs($this->admin)->post(route('admin.hr.recruitment.candidates.convert', $candidate->id), [
            'department_id' => $dept->id,
            'position_id' => $pos->id,
            'basic_salary' => 9500,
            'hire_date' => now()->toDateString(),
        ]);
        $convertResponse->assertRedirect();
        $this->assertDatabaseHas('employees', ['email' => 'kareem.fahmy@example.com']);
    }

    public function test_attendance_and_overtime_management(): void
    {
        $employee = Employee::create([
            'employee_number' => 'EMP-990011',
            'first_name' => 'Sultan',
            'last_name' => 'Zahran',
            'email' => 'sultan@bluezone.com',
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'hire_date' => now()->toDateString(),
            'basic_salary' => 9000,
        ]);

        $checkInResponse = $this->actingAs($this->admin)->post(route('admin.hr.attendance.check-in'), [
            'employee_id' => $employee->id,
            'attendance_date' => now()->toDateString(),
            'check_in' => '08:55',
            'source' => 'web',
        ]);
        $checkInResponse->assertRedirect();
        $this->assertDatabaseHas('attendance_records', [
            'employee_id' => $employee->id,
        ]);

        $otResponse = $this->actingAs($this->admin)->post(route('admin.hr.attendance.overtime.store'), [
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'start_time' => '17:00',
            'end_time' => '19:00',
            'minutes' => 120,
            'reason' => 'Inventory Audit Preparation',
        ]);
        $otResponse->assertRedirect();
        $this->assertDatabaseHas('overtime_requests', [
            'employee_id' => $employee->id,
            'minutes' => 120,
        ]);
    }

    public function test_leave_requests(): void
    {
        $employee = Employee::create([
            'employee_number' => 'EMP-990022',
            'first_name' => 'Noura',
            'last_name' => 'Al-Harbi',
            'email' => 'noura@bluezone.com',
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'hire_date' => now()->toDateString(),
            'basic_salary' => 11000,
        ]);

        $leaveType = LeaveType::create([
            'name_en' => 'Annual Leave',
            'name_ar' => 'إجازة سنوية',
            'code' => 'ANNUAL',
            'annual_days' => 21,
            'is_paid' => true,
            'is_active' => true,
        ]);

        $leaveResponse = $this->actingAs($this->admin)->post(route('admin.hr.leave.requests.store'), [
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(8)->toDateString(),
            'reason' => 'Family Vacation',
        ]);
        $leaveResponse->assertRedirect();
        $this->assertDatabaseHas('leave_requests', [
            'employee_id' => $employee->id,
            'status' => 'pending',
        ]);
    }

    public function test_payroll_period_and_calculation(): void
    {
        $periodResponse = $this->actingAs($this->admin)->post(route('admin.hr.payroll.periods.store'), [
            'name' => 'Test Batch Month',
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
            'payment_date' => now()->endOfMonth()->toDateString(),
        ]);
        $periodResponse->assertRedirect();
        $this->assertDatabaseHas('payroll_periods', ['name' => 'Test Batch Month']);

        $period = PayrollPeriod::where('name', 'Test Batch Month')->first();

        $genResponse = $this->actingAs($this->admin)->post(route('admin.hr.payroll.periods.generate', $period->id));
        $genResponse->assertRedirect();

        $finResponse = $this->actingAs($this->admin)->post(route('admin.hr.payroll.periods.finalize', $period->id));
        $finResponse->assertRedirect();

        $period->refresh();
        $this->assertEquals('finalized', $period->status);
    }

    public function test_user_creation_automatically_syncs_to_hr_employee(): void
    {
        $role = Role::firstOrCreate(['name' => 'Sales Staff'], ['description' => 'Sales', 'permissions' => '[]']);
        $user = User::create([
            'name' => 'Fahad Al-Harbi',
            'email' => 'fahad.harbi@bluezone.com',
            'password' => bcrypt('secret123'),
            'role_id' => $role->id,
            'status' => 'active',
            'phone' => '+966509998877',
        ]);

        $this->assertDatabaseHas('employees', [
            'user_id' => $user->id,
            'email' => 'fahad.harbi@bluezone.com',
            'first_name' => 'Fahad',
            'last_name' => 'Al-Harbi',
        ]);

        // Verify update sync
        $user->update(['name' => 'Fahad Al-Dosari']);
        $this->assertDatabaseHas('employees', [
            'user_id' => $user->id,
            'last_name' => 'Al-Dosari',
        ]);
    }
}
