<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeSelfServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_employee_can_check_in_and_check_out_via_self_service(): void
    {
        $user = User::factory()->create();
        $employee = $user->getOrCreateEmployee();

        // 1. Check in
        $response = $this->actingAs($user)->postJson(route('admin.self-service.check-in'));
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('attendance_records', [
            'employee_id' => $employee->id,
            'source' => 'web',
        ]);

        // Duplicate check in should fail gracefully
        $dupResponse = $this->actingAs($user)->postJson(route('admin.self-service.check-in'));
        $dupResponse->assertStatus(422);

        // 2. Check out
        $outResponse = $this->actingAs($user)->postJson(route('admin.self-service.check-out'));
        $outResponse->assertStatus(200);
        $outResponse->assertJson(['success' => true]);

        $record = AttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('attendance_date', now()->toDateString())
            ->first();

        $this->assertNotNull($record);
        $this->assertNotNull($record->check_out);
    }

    public function test_employee_can_submit_leave_request_and_reserve_balance(): void
    {
        $user = User::factory()->create();
        $employee = $user->getOrCreateEmployee();

        $leaveType = LeaveType::firstOrCreate(
            ['code' => 'annual'],
            [
                'name_en' => 'Annual Leave',
                'name_ar' => 'إجازة سنوية',
                'annual_days' => 21,
                'is_active' => true,
            ]
        );

        $startDate = now()->addDays(5)->toDateString();
        $endDate = now()->addDays(7)->toDateString(); // 3 days

        $response = $this->actingAs($user)->postJson(route('admin.self-service.leave'), [
            'leave_type_id' => $leaveType->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => 'Family vacation and personal travel.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('leave_requests', [
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'total_days' => 3.00,
            'status' => 'pending',
        ]);

        // Verify balance was reserved
        $balance = LeaveBalance::where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveType->id)
            ->first();

        $this->assertNotNull($balance);
        $this->assertEquals(3.00, (float) $balance->pending_days);
    }

    public function test_self_service_status_endpoint_returns_valid_payload(): void
    {
        $user = User::factory()->create();
        $employee = $user->getOrCreateEmployee();

        $response = $this->actingAs($user)->getJson(route('admin.self-service.status'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'employee' => ['id', 'name', 'number', 'department', 'position'],
            'attendance' => ['date', 'has_record', 'is_checked_in', 'is_checked_out', 'status'],
            'leave_types',
            'recent_leaves',
            'server_time',
        ]);
    }

    public function test_admin_can_approve_and_reject_leave_requests(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Admin', 'permissions' => ['*']]);
        $admin = User::factory()->create(['role_id' => $role->id]);
        $employeeUser = User::factory()->create();
        $employee = $employeeUser->getOrCreateEmployee();

        $leaveType = LeaveType::firstOrCreate(
            ['code' => 'annual'],
            ['name_en' => 'Annual Leave', 'name_ar' => 'إجازة سنوية', 'annual_days' => 21, 'is_active' => true]
        );

        $leaveRequest = LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'total_days' => 3.00,
            'reason' => 'Rest and recreation',
            'status' => 'pending',
        ]);

        // Approve
        $approveResponse = $this->actingAs($admin)->post(route('admin.hr.leave.requests.approve', $leaveRequest->id));
        $approveResponse->assertSessionHas('success');
        $this->assertEquals('approved', $leaveRequest->fresh()->status);

        // Create another request to test reject
        $rejectLeave = LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDays(10)->toDateString(),
            'end_date' => now()->addDays(12)->toDateString(),
            'total_days' => 3.00,
            'reason' => 'Emergency personal time',
            'status' => 'pending',
        ]);

        $rejectResponse = $this->actingAs($admin)->post(route('admin.hr.leave.requests.reject', $rejectLeave->id), [
            'rejection_reason' => 'Team capacity constraints during high volume cycle.',
        ]);
        $rejectResponse->assertSessionHas('success');
        $this->assertEquals('rejected', $rejectLeave->fresh()->status);
        $this->assertEquals('Team capacity constraints during high volume cycle.', $rejectLeave->fresh()->rejection_reason);
    }
}
