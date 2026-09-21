<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\HrAsset;
use App\Models\LeaveType;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Models\WorkScheduleDay;
use App\Services\Hr\EmployeeNumberService;
use App\Services\Hr\EmployeeService;
use Illuminate\Database\Seeder;

class HrSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Departments
        $departments = [
            ['code' => 'DEP-MGMT', 'name_en' => 'Management', 'name_ar' => 'الإدارة العامة', 'description' => 'Executive & strategic corporate management'],
            ['code' => 'DEP-HR', 'name_en' => 'Human Resources', 'name_ar' => 'الموارد البشرية', 'description' => 'Human talent, talent acquisition, and employee relations'],
            ['code' => 'DEP-SALES', 'name_en' => 'Commercial Sales', 'name_ar' => 'المبيعات التجارية', 'description' => 'Direct B2B and retail sales operations'],
            ['code' => 'DEP-MKT', 'name_en' => 'Marketing', 'name_ar' => 'التسويق', 'description' => 'Brand strategy, advertising and campaigns'],
            ['code' => 'DEP-MED', 'name_en' => 'Medical Affairs', 'name_ar' => 'الشؤون الطبية والميدانية', 'description' => 'Medical representatives, physician relations, and clinical communication'],
            ['code' => 'DEP-FIN', 'name_en' => 'Finance & Accounting', 'name_ar' => 'المالية والمحاسبة', 'description' => 'Fiscal compliance, budget planning and accounts'],
            ['code' => 'DEP-OPS', 'name_en' => 'Operations & Logistics', 'name_ar' => 'العمليات وسلاسل الإمداد', 'description' => 'Supply chain, multi-hubs, and order fulfillment'],
            ['code' => 'DEP-IT', 'name_en' => 'Information Technology', 'name_ar' => 'تقنية المعلومات', 'description' => 'Software systems, infrastructure, and security'],
        ];

        $deptModels = [];
        foreach ($departments as $d) {
            $deptModels[$d['code']] = Department::firstOrCreate(['code' => $d['code']], $d);
        }

        // 2. Positions
        $positions = [
            ['department_id' => $deptModels['DEP-MGMT']->id, 'code' => 'POS-CEO', 'name_en' => 'Chief Executive Officer', 'name_ar' => 'الرئيس التنفيذي', 'level' => 'director'],
            ['department_id' => $deptModels['DEP-HR']->id, 'code' => 'POS-HRM', 'name_en' => 'HR Manager', 'name_ar' => 'مدير الموارد البشرية', 'level' => 'manager'],
            ['department_id' => $deptModels['DEP-HR']->id, 'code' => 'POS-HRS', 'name_en' => 'HR Specialist', 'name_ar' => 'أخصائي موارد بشرية', 'level' => 'intermediate'],
            ['department_id' => $deptModels['DEP-SALES']->id, 'code' => 'POS-SM', 'name_en' => 'Sales Manager', 'name_ar' => 'مدير المبيعات', 'level' => 'manager'],
            ['department_id' => $deptModels['DEP-SALES']->id, 'code' => 'POS-SR', 'name_en' => 'Sales Representative', 'name_ar' => 'مندوب مبيعات', 'level' => 'intermediate'],
            ['department_id' => $deptModels['DEP-MED']->id, 'code' => 'POS-MRM', 'name_en' => 'Medical Field Manager', 'name_ar' => 'مدير الفريق الطبي الميداني', 'level' => 'manager'],
            ['department_id' => $deptModels['DEP-MED']->id, 'code' => 'POS-MR', 'name_en' => 'Medical Representative', 'name_ar' => 'مندوب دعاية طبية', 'level' => 'intermediate'],
            ['department_id' => $deptModels['DEP-FIN']->id, 'code' => 'POS-ACC', 'name_en' => 'Senior Accountant', 'name_ar' => 'محاسب أول', 'level' => 'senior'],
            ['department_id' => $deptModels['DEP-OPS']->id, 'code' => 'POS-OPM', 'name_en' => 'Logistics & Warehouse Manager', 'name_ar' => 'مدير المستودعات والعمليات', 'level' => 'manager'],
            ['department_id' => $deptModels['DEP-IT']->id, 'code' => 'POS-ITS', 'name_en' => 'Senior Software Engineer', 'name_ar' => 'مهندس برمجيات أول', 'level' => 'senior'],
        ];

        $posModels = [];
        foreach ($positions as $p) {
            $posModels[$p['code']] = Position::firstOrCreate(['code' => $p['code']], $p);
        }

        // 3. Work Schedules
        $schedules = [
            [
                'name_en' => 'Standard Office 9-5',
                'name_ar' => 'الدوام المكتبي المعتاد 9-5',
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'break_minutes' => 60,
                'grace_minutes' => 15,
                'working_hours' => 8.00,
            ],
            [
                'name_en' => 'Standard Commercial 10-6',
                'name_ar' => 'الدوام التجاري 10-6',
                'start_time' => '10:00:00',
                'end_time' => '18:00:00',
                'break_minutes' => 60,
                'grace_minutes' => 20,
                'working_hours' => 8.00,
            ],
            [
                'name_en' => 'Field Flexible Schedule',
                'name_ar' => 'الدوام الميداني المرن',
                'start_time' => '08:30:00',
                'end_time' => '16:30:00',
                'break_minutes' => 60,
                'grace_minutes' => 30,
                'working_hours' => 8.00,
            ],
        ];

        $scheduleModels = [];
        foreach ($schedules as $s) {
            $sched = WorkSchedule::firstOrCreate(['name_en' => $s['name_en']], $s);
            $scheduleModels[] = $sched;

            // Days: Sun(0) through Thu(4) are working days, Fri(5) & Sat(6) are weekend
            for ($day = 0; $day <= 6; $day++) {
                WorkScheduleDay::firstOrCreate(
                    ['work_schedule_id' => $sched->id, 'day_of_week' => $day],
                    ['is_working_day' => ($day <= 4)]
                );
            }
        }

        // 4. Leave Types
        $leaveTypes = [
            ['code' => 'annual', 'name_en' => 'Annual Leave', 'name_ar' => 'إجازة سنوية', 'annual_days' => 21, 'is_paid' => true, 'requires_approval' => true],
            ['code' => 'sick', 'name_en' => 'Sick Leave', 'name_ar' => 'إجازة مرضية', 'annual_days' => 14, 'is_paid' => true, 'requires_attachment' => true, 'requires_approval' => true],
            ['code' => 'emergency', 'name_en' => 'Emergency Leave', 'name_ar' => 'إجازة طارئة', 'annual_days' => 5, 'is_paid' => true, 'requires_approval' => true],
            ['code' => 'unpaid', 'name_en' => 'Unpaid Leave', 'name_ar' => 'إجازة بدون راتب', 'annual_days' => 30, 'is_paid' => false, 'requires_approval' => true],
            ['code' => 'maternity', 'name_en' => 'Maternity Leave', 'name_ar' => 'إجازة وضع وأمومة', 'annual_days' => 70, 'is_paid' => true, 'requires_attachment' => true, 'requires_approval' => true],
            ['code' => 'paternity', 'name_en' => 'Paternity Leave', 'name_ar' => 'إجازة أبوة', 'annual_days' => 3, 'is_paid' => true, 'requires_approval' => true],
            ['code' => 'study', 'name_en' => 'Examination / Study', 'name_ar' => 'إجازة دراسة واختبارات', 'annual_days' => 10, 'is_paid' => true, 'requires_attachment' => true, 'requires_approval' => true],
        ];

        foreach ($leaveTypes as $lt) {
            LeaveType::firstOrCreate(['code' => $lt['code']], $lt);
        }

        // 5. Assets
        $assets = [
            ['asset_code' => 'AST-LPT-001', 'name' => 'MacBook Pro 16 M3', 'category' => 'laptop', 'serial_number' => 'MBP-2026-9921', 'model' => 'Apple M3 Pro', 'purchase_date' => '2026-01-15', 'cost' => 12500.00, 'status' => 'available'],
            ['asset_code' => 'AST-LPT-002', 'name' => 'Dell Latitude 7440', 'category' => 'laptop', 'serial_number' => 'DEL-7440-8812', 'model' => 'Dell Latitude', 'purchase_date' => '2026-02-10', 'cost' => 5800.00, 'status' => 'available'],
            ['asset_code' => 'AST-PHN-001', 'name' => 'iPhone 15 Pro Max', 'category' => 'phone', 'serial_number' => 'IPH-15-5541', 'model' => 'Apple iPhone', 'purchase_date' => '2026-02-01', 'cost' => 5200.00, 'status' => 'available'],
            ['asset_code' => 'AST-SIM-001', 'name' => 'STC Corporate 5G SIM', 'category' => 'sim', 'serial_number' => '89966010023412', 'model' => 'STC Business Data & Voice', 'purchase_date' => '2026-01-01', 'cost' => 350.00, 'status' => 'available'],
        ];

        foreach ($assets as $a) {
            HrAsset::firstOrCreate(['asset_code' => $a['asset_code']], $a);
        }

        // 6. Seed HR Role & Grant permissions to Admin / Super Admin
        $hrPermissions = [
            'hr.view',
            'hr.dashboard.view',
            'employees.view',
            'employees.create',
            'employees.update',
            'employees.delete',
            'employee_documents.view',
            'employee_documents.create',
            'employee_documents.update',
            'employee_documents.delete',
            'departments.view',
            'departments.create',
            'departments.update',
            'departments.delete',
            'positions.view',
            'positions.create',
            'positions.update',
            'positions.delete',
            'attendance.view',
            'attendance.create',
            'attendance.update',
            'attendance.manage',
            'overtime.view',
            'overtime.create',
            'overtime.approve',
            'leave.view',
            'leave.create',
            'leave.update',
            'leave.approve',
            'payroll.view',
            'payroll.create',
            'payroll.update',
            'payroll.approve',
            'payroll.finalize',
            'advances.view',
            'advances.create',
            'advances.approve',
            'loans.view',
            'loans.create',
            'loans.approve',
            'performance.view',
            'performance.create',
            'performance.update',
            'performance.approve',
            'training.view',
            'training.create',
            'training.update',
            'recruitment.view',
            'recruitment.create',
            'recruitment.update',
            'recruitment.manage',
            'contracts.view',
            'contracts.create',
            'contracts.update',
            'assets.view',
            'assets.create',
            'assets.update',
            'assets.assign',
            'employee_requests.view',
            'employee_requests.manage',
            'offboarding.view',
            'offboarding.manage',
            'reports.hr.view',
            'hr.settings.manage',
        ];

        $hrRole = Role::firstOrCreate(
            ['name' => 'HR Manager'],
            [
                'description' => 'Complete management access to Human Resources platform.',
                'permissions' => json_encode($hrPermissions),
                'users_count' => 1,
            ]
        );

        // 7. Seed Employees (automatically synchronizing all system users into HR module)
        $userObserver = new \App\Observers\UserObserver();
        foreach (User::all() as $systemUser) {
            $userObserver->syncEmployeeProfile($systemUser);
        }
    }
}
