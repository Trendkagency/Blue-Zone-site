<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\Employee;
use App\Models\AttendanceRecord;
use App\Models\Country;
use App\Models\City;
use App\Models\Area;
use App\Models\AreaBreak;
use App\Models\Mr\ContactSpecialty;
use App\Models\Mr\ContactClassification;
use App\Models\Mr\Contact;
use App\Models\Mr\VisitCycle;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\ScheduledVisit;
use App\Models\Mr\Visit;

class FreshProductionDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        echo "=== 1. Setting up Clean Roles & Granular Permissions ===\n";

        // 1. Super Admin
        $superAdminRole = Role::updateOrCreate(['id' => 3], [
            'name' => 'Super Admin',
            'description' => 'Unrestricted root authority across all application modules and settings.',
            'permissions' => ['*'],
        ]);

        // 2. MR Line Manager
        $mrLineManagerRole = Role::updateOrCreate(['id' => 2], [
            'name' => 'MR Line Manager',
            'description' => 'Field supervisor overseeing medical reps, doctor portfolios, coverage plans, and approvals.',
            'permissions' => [
                'mr.*',
                'mr_dashboard.*',
                'mr_visits.*',
                'mr_contacts.*',
                'mr_assignments.*',
                'mr_territories.*',
                'mr_reports.*',
                'mr_cycles.*',
                'mr_gps.*',
                'products.view',
                'hr_attendance.view',
            ],
        ]);

        // 3. Medical Representative (MR)
        $mrRole = Role::updateOrCreate(['id' => 1], [
            'name' => 'Medical Representative (MR)',
            'description' => 'Field sales representative focused on doctors, clinic visits, schedules, and territory coverage.',
            'permissions' => [
                'mr_dashboard.view' => true,
                'mr_dashboard.create' => true,
                'mr_dashboard.edit' => true,
                'mr_visits.view' => true,
                'mr_visits.create' => true,
                'mr_contacts.view' => true,
                'mr_reports.view' => true,
                'products.view' => true,
                'hr_attendance.create' => true,
            ],
        ]);

        // 4. Warehouse & Inventory Officer
        $inventoryRole = Role::updateOrCreate(['id' => 6], [
            'name' => 'Inventory Officer',
            'description' => 'Manages warehouse movements, lot receiving, dispatching, stock audits, and counts.',
            'permissions' => [
                'inventory.*',
                'products.view',
                'products.edit',
                'orders.view',
                'orders.edit',
                'reports.view',
            ],
        ]);

        // 5. HR Manager
        $hrRole = Role::updateOrCreate(['id' => 7], [
            'name' => 'HR Manager',
            'description' => 'Manages personnel dossiers, attendance time clock approvals, departments, shifts, and compensation.',
            'permissions' => [
                'hr.*',
                'hr_employees.*',
                'hr_attendance.*',
                'hr_departments.*',
                'hr_payroll.*',
                'users.view',
                'notifications.*',
            ],
        ]);

        // 6. Commercial Accounts Rep (B2B Product Deals)
        $commercialRole = Role::updateOrCreate(['id' => 5], [
            'name' => 'Commercial Accounts Rep',
            'description' => 'Commercial B2B corporate sales representative focused on pharmacy chain deals and wholesale product distribution.',
            'permissions' => [
                'crm_leads.view' => true,
                'crm_leads.create' => true,
                'crm_leads.edit' => true,
                'crm_opportunities.view' => true,
                'crm_opportunities.create' => true,
                'crm_opportunities.edit' => true,
                'crm_activities.view' => true,
                'crm_activities.create' => true,
                'crm_activities.edit' => true,
                'products.view' => true,
                'customers.view' => true,
                'customers.create' => true,
            ],
        ]);

        echo "=== 2. Egyptian Geographic Hierarchy (Country, Cities, Areas & Breaks) ===\n";

        // Country: Egypt
        $egypt = Country::updateOrCreate(['iso2' => 'EG'], [
            'name_en' => 'Egypt',
            'name_ar' => 'جمهورية مصر العربية',
            'phone_code' => '20',
            'currency_code' => 'EGP',
            'is_active' => true,
        ]);

        // 1. Cairo
        $cairo = City::updateOrCreate(['name_en' => 'Cairo', 'country_id' => $egypt->id], [
            'name_ar' => 'القاهرة',
            'is_active' => true,
        ]);

        // 2. Giza
        $giza = City::updateOrCreate(['name_en' => 'Giza', 'country_id' => $egypt->id], [
            'name_ar' => 'الجيزة',
            'is_active' => true,
        ]);

        // 3. Alexandria
        $alex = City::updateOrCreate(['name_en' => 'Alexandria', 'country_id' => $egypt->id], [
            'name_ar' => 'الإسكندرية',
            'is_active' => true,
        ]);

        // 4. Mansoura
        $mansoura = City::updateOrCreate(['name_en' => 'Mansoura', 'country_id' => $egypt->id], [
            'name_ar' => 'المنصورة (الدقهلية)',
            'is_active' => true,
        ]);

        // --- Cairo Areas & Breaks ---
        $nasrCityArea = Area::updateOrCreate(['code' => 'CAI-NC'], [
            'country_id' => $egypt->id,
            'city_id' => $cairo->id,
            'name_en' => 'Nasr City Territory',
            'name_ar' => 'قطاع مدينة نصر',
            'is_active' => true,
        ]);
        $brkAbbas = AreaBreak::updateOrCreate(['code' => 'BRK-NC-01'], [
            'country_id' => $egypt->id, 'city_id' => $cairo->id, 'area_id' => $nasrCityArea->id,
            'name_en' => 'Abbas El-Akkad St', 'name_ar' => 'شارع عباس العقاد', 'is_active' => true, 'sort_order' => 1,
        ]);
        $brkMakram = AreaBreak::updateOrCreate(['code' => 'BRK-NC-02'], [
            'country_id' => $egypt->id, 'city_id' => $cairo->id, 'area_id' => $nasrCityArea->id,
            'name_en' => 'Makram Ebeid St', 'name_ar' => 'شارع مكرم عبيد', 'is_active' => true, 'sort_order' => 2,
        ]);
        $brkTayaran = AreaBreak::updateOrCreate(['code' => 'BRK-NC-03'], [
            'country_id' => $egypt->id, 'city_id' => $cairo->id, 'area_id' => $nasrCityArea->id,
            'name_en' => 'El-Tayaran St & Rabia', 'name_ar' => 'شارع الطيران ورابعة', 'is_active' => true, 'sort_order' => 3,
        ]);

        $heliopolisArea = Area::updateOrCreate(['code' => 'CAI-HEL'], [
            'country_id' => $egypt->id,
            'city_id' => $cairo->id,
            'name_en' => 'Heliopolis & Sheraton Territory',
            'name_ar' => 'قطاع مصر الجديدة وشيراتون',
            'is_active' => true,
        ]);
        $brkKorba = AreaBreak::updateOrCreate(['code' => 'BRK-HEL-01'], [
            'country_id' => $egypt->id, 'city_id' => $cairo->id, 'area_id' => $heliopolisArea->id,
            'name_en' => 'El-Korba Heritage District', 'name_ar' => 'الكوربة التراثية', 'is_active' => true, 'sort_order' => 1,
        ]);
        $brkRoxy = AreaBreak::updateOrCreate(['code' => 'BRK-HEL-02'], [
            'country_id' => $egypt->id, 'city_id' => $cairo->id, 'area_id' => $heliopolisArea->id,
            'name_en' => 'Roxy Medical Hub', 'name_ar' => 'روكسي والميديكال سنتر', 'is_active' => true, 'sort_order' => 2,
        ]);
        $brkSheraton = AreaBreak::updateOrCreate(['code' => 'BRK-HEL-03'], [
            'country_id' => $egypt->id, 'city_id' => $cairo->id, 'area_id' => $heliopolisArea->id,
            'name_en' => 'Sheraton Heliopolis', 'name_ar' => 'مساكن شيراتون المطار', 'is_active' => true, 'sort_order' => 3,
        ]);

        $newCairoArea = Area::updateOrCreate(['code' => 'CAI-NC5'], [
            'country_id' => $egypt->id,
            'city_id' => $cairo->id,
            'name_en' => 'New Cairo & 5th Settlement',
            'name_ar' => 'قطاع التجمع الخامس والقاهرة الجديدة',
            'is_active' => true,
        ]);
        $brk90North = AreaBreak::updateOrCreate(['code' => 'BRK-NC5-01'], [
            'country_id' => $egypt->id, 'city_id' => $cairo->id, 'area_id' => $newCairoArea->id,
            'name_en' => 'North 90th Street Medical Complexes', 'name_ar' => 'شارع التسعين الشمالي والمجمعات الطبية', 'is_active' => true, 'sort_order' => 1,
        ]);
        $brk90South = AreaBreak::updateOrCreate(['code' => 'BRK-NC5-02'], [
            'country_id' => $egypt->id, 'city_id' => $cairo->id, 'area_id' => $newCairoArea->id,
            'name_en' => 'South 90th Street & Concord Plaza', 'name_ar' => 'شارع التسعين الجنوبي وكونكورد بلازا', 'is_active' => true, 'sort_order' => 2,
        ]);
        $brkMedPark = AreaBreak::updateOrCreate(['code' => 'BRK-NC5-03'], [
            'country_id' => $egypt->id, 'city_id' => $cairo->id, 'area_id' => $newCairoArea->id,
            'name_en' => 'Medical Park Premier & Elite', 'name_ar' => 'ميديكال بارك بريمير وإيليت', 'is_active' => true, 'sort_order' => 3,
        ]);

        $maadiArea = Area::updateOrCreate(['code' => 'CAI-MAA'], [
            'country_id' => $egypt->id,
            'city_id' => $cairo->id,
            'name_en' => 'Maadi Territory',
            'name_ar' => 'قطاع المعادي',
            'is_active' => true,
        ]);
        $brkDegla = AreaBreak::updateOrCreate(['code' => 'BRK-MAA-01'], [
            'country_id' => $egypt->id, 'city_id' => $cairo->id, 'area_id' => $maadiArea->id,
            'name_en' => 'Degla Maadi & St. 233', 'name_ar' => 'دجلة المعادي وشارع 233', 'is_active' => true, 'sort_order' => 1,
        ]);
        $brkCornicheMaadi = AreaBreak::updateOrCreate(['code' => 'BRK-MAA-02'], [
            'country_id' => $egypt->id, 'city_id' => $cairo->id, 'area_id' => $maadiArea->id,
            'name_en' => 'Corniche El-Maadi Hospitals Zone', 'name_ar' => 'كورنيش المعادي ومنطقة المستشفيات', 'is_active' => true, 'sort_order' => 2,
        ]);

        // --- Giza Areas & Breaks ---
        $dokkiArea = Area::updateOrCreate(['code' => 'GIZ-DOK'], [
            'country_id' => $egypt->id,
            'city_id' => $giza->id,
            'name_en' => 'Dokki & Mohandessin Territory',
            'name_ar' => 'قطاع الدقي والمهندسين',
            'is_active' => true,
        ]);
        $brkMosaddak = AreaBreak::updateOrCreate(['code' => 'BRK-DOK-01'], [
            'country_id' => $egypt->id, 'city_id' => $giza->id, 'area_id' => $dokkiArea->id,
            'name_en' => 'Mosaddak St Medical Hub', 'name_ar' => 'شارع مصدق الطبي', 'is_active' => true, 'sort_order' => 1,
        ]);
        $brkDowal = AreaBreak::updateOrCreate(['code' => 'BRK-DOK-02'], [
            'country_id' => $egypt->id, 'city_id' => $giza->id, 'area_id' => $dokkiArea->id,
            'name_en' => 'Gameat Al-Dowal & Shehab', 'name_ar' => 'جامعة الدول العربية وشهاب', 'is_active' => true, 'sort_order' => 2,
        ]);
        $brkBatal = AreaBreak::updateOrCreate(['code' => 'BRK-DOK-03'], [
            'country_id' => $egypt->id, 'city_id' => $giza->id, 'area_id' => $dokkiArea->id,
            'name_en' => 'Batal Ahmed Abdelaziz St', 'name_ar' => 'شارع بطل أحمد عبد العزيز', 'is_active' => true, 'sort_order' => 3,
        ]);

        $zayedArea = Area::updateOrCreate(['code' => 'GIZ-ZAY'], [
            'country_id' => $egypt->id,
            'city_id' => $giza->id,
            'name_en' => '6th October & Sheikh Zayed',
            'name_ar' => 'قطاع 6 أكتوبر والشيخ زايد',
            'is_active' => true,
        ]);
        $brkArkan = AreaBreak::updateOrCreate(['code' => 'BRK-ZAY-01'], [
            'country_id' => $egypt->id, 'city_id' => $giza->id, 'area_id' => $zayedArea->id,
            'name_en' => 'Arkan Plaza & Capital Business', 'name_ar' => 'أركان بلازا وكابيتال بيزنس بارك', 'is_active' => true, 'sort_order' => 1,
        ]);
        $brkHosary = AreaBreak::updateOrCreate(['code' => 'BRK-ZAY-02'], [
            'country_id' => $egypt->id, 'city_id' => $giza->id, 'area_id' => $zayedArea->id,
            'name_en' => 'Hosary Square & Central Spine', 'name_ar' => 'ميدان الحصري والمحور المركزي', 'is_active' => true, 'sort_order' => 2,
        ]);

        // --- Alexandria Areas & Breaks ---
        $smouhaArea = Area::updateOrCreate(['code' => 'ALX-SMO'], [
            'country_id' => $egypt->id,
            'city_id' => $alex->id,
            'name_en' => 'Smouha & Sidi Gaber Territory',
            'name_ar' => 'قطاع سموحة وسيدي جابر',
            'is_active' => true,
        ]);
        $brkSmouha = AreaBreak::updateOrCreate(['code' => 'BRK-SMO-01'], [
            'country_id' => $egypt->id, 'city_id' => $alex->id, 'area_id' => $smouhaArea->id,
            'name_en' => 'Victor Emmanuel & Smouha Sq', 'name_ar' => 'شارع فيكتور عمانويل وميدان سموحة', 'is_active' => true, 'sort_order' => 1,
        ]);

        $loranArea = Area::updateOrCreate(['code' => 'ALX-LOR'], [
            'country_id' => $egypt->id,
            'city_id' => $alex->id,
            'name_en' => 'Loran & Ramleh Territory',
            'name_ar' => 'قطاع لوران ومحطة الرمل',
            'is_active' => true,
        ]);
        $brkLoran = AreaBreak::updateOrCreate(['code' => 'BRK-LOR-01'], [
            'country_id' => $egypt->id, 'city_id' => $alex->id, 'area_id' => $loranArea->id,
            'name_en' => 'Loran Abu Qir Rd', 'name_ar' => 'لوران طريق الحرية وأبو قير', 'is_active' => true, 'sort_order' => 1,
        ]);

        echo "=== 3. Departments and Job Positions ===\n";
        $medicalDept = Department::updateOrCreate(['code' => 'MED-OPS'], [
            'name_en' => 'Medical Field Operations',
            'name_ar' => 'العمليات الميدانية والدعاية الطبية',
            'is_active' => true,
        ]);
        $hrDept = Department::updateOrCreate(['code' => 'HR'], [
            'name_en' => 'Human Resources',
            'name_ar' => 'الموارد البشرية والشؤون الإدارية',
            'is_active' => true,
        ]);
        $supplyDept = Department::updateOrCreate(['code' => 'SCM'], [
            'name_en' => 'Warehouse & Supply Chain',
            'name_ar' => 'المستودعات وسلاسل الإمداد',
            'is_active' => true,
        ]);

        $posLineManager = Position::updateOrCreate(['code' => 'POS-MRLM'], [
            'name_en' => 'MR Line Manager & Field Supervisor',
            'name_ar' => 'مشرف ومسؤول مناديب الدعاية الطبية',
            'department_id' => $medicalDept->id,
            'is_active' => true,
        ]);
        $posMedicalRep = Position::updateOrCreate(['code' => 'POS-MR'], [
            'name_en' => 'Senior Medical Representative',
            'name_ar' => 'مندوب دعاية طبية أول',
            'department_id' => $medicalDept->id,
            'is_active' => true,
        ]);
        $posWarehouse = Position::updateOrCreate(['code' => 'POS-WH'], [
            'name_en' => 'Warehouse & Inventory Officer',
            'name_ar' => 'مسؤول المستودعات والمخزون',
            'department_id' => $supplyDept->id,
            'is_active' => true,
        ]);
        $posHr = Position::updateOrCreate(['code' => 'POS-HR'], [
            'name_en' => 'HR & Workforce Manager',
            'name_ar' => 'مدير شؤون الموظفين والموارد البشرية',
            'department_id' => $hrDept->id,
            'is_active' => true,
        ]);

        echo "=== 4. Seeding Egyptian Staff Users & Employee Profiles ===\n";
        $today = Carbon::today();

        // 1. Super Admin: Tariq M.
        $superAdmin = User::updateOrCreate(['id' => 10], [
            'name' => 'Tariq M.',
            'email' => 'tariq@bluezone.com',
            'password' => Hash::make('password'),
            'role_id' => $superAdminRole->id,
            'status' => 'active',
            'phone' => '+201000000001',
            'country_id' => $egypt->id,
            'city_id' => $cairo->id,
            'area_id' => $heliopolisArea->id,
            'break_id' => $brkKorba->id,
        ]);

        // 2. MR Line Manager: Dr. Ahmed Ezzat
        $lineManager = User::updateOrCreate(['email' => 'ahmed.ezzat@bluezone.com'], [
            'name' => 'Dr. Ahmed Ezzat (Line Manager)',
            'password' => Hash::make('password'),
            'role_id' => $mrLineManagerRole->id,
            'status' => 'active',
            'phone' => '+201011122334',
            'country_id' => $egypt->id,
            'city_id' => $cairo->id,
            'area_id' => $heliopolisArea->id,
            'break_id' => $brkRoxy->id,
        ]);

        $empLineMgr = Employee::updateOrCreate(['user_id' => $lineManager->id], [
            'employee_number' => 'EMP-EG-001',
            'first_name' => 'Ahmed',
            'last_name' => 'Ezzat',
            'email' => $lineManager->email,
            'phone' => $lineManager->phone,
            'department_id' => $medicalDept->id,
            'position_id' => $posLineManager->id,
            'employment_status' => 'active',
            'hire_date' => '2024-01-15',
        ]);

        AttendanceRecord::updateOrCreate([
            'employee_id' => $empLineMgr->id,
            'attendance_date' => $today,
        ], [
            'check_in' => '08:15:00',
            'status' => 'present',
            'worked_minutes' => 480,
            'source' => 'system',
        ]);

        // 3. Medical Representative 1: Dr. Kareem Tarek (User ID 9) - Cairo (Nasr City)
        $kareem = User::updateOrCreate(['id' => 9], [
            'name' => 'Dr. Kareem Tarek (MR)',
            'email' => 'kareem.tarek@bluezone.com',
            'password' => Hash::make('password'),
            'role_id' => $mrRole->id,
            'status' => 'active',
            'phone' => '+201022233445',
            'country_id' => $egypt->id,
            'city_id' => $cairo->id,
            'area_id' => $nasrCityArea->id,
            'break_id' => $brkAbbas->id,
        ]);

        $empKareem = Employee::updateOrCreate(['user_id' => $kareem->id], [
            'employee_number' => 'EMP-EG-002',
            'first_name' => 'Kareem',
            'last_name' => 'Tarek',
            'email' => $kareem->email,
            'phone' => $kareem->phone,
            'department_id' => $medicalDept->id,
            'position_id' => $posMedicalRep->id,
            'employment_status' => 'active',
            'hire_date' => '2024-03-01',
        ]);

        AttendanceRecord::updateOrCreate([
            'employee_id' => $empKareem->id,
            'attendance_date' => $today,
        ], [
            'check_in' => '08:45:00',
            'status' => 'present',
            'worked_minutes' => 480,
            'source' => 'mobile_gps',
        ]);

        // 4. Medical Representative 2: Dr. Mahmoud Hassan - Cairo (New Cairo & 5th Settlement)
        $mahmoud = User::updateOrCreate(['email' => 'mahmoud.hassan@bluezone.com'], [
            'name' => 'Dr. Mahmoud Hassan (MR)',
            'password' => Hash::make('password'),
            'role_id' => $mrRole->id,
            'status' => 'active',
            'phone' => '+201033344556',
            'country_id' => $egypt->id,
            'city_id' => $cairo->id,
            'area_id' => $newCairoArea->id,
            'break_id' => $brk90North->id,
        ]);

        $empMahmoud = Employee::updateOrCreate(['user_id' => $mahmoud->id], [
            'employee_number' => 'EMP-EG-003',
            'first_name' => 'Mahmoud',
            'last_name' => 'Hassan',
            'email' => $mahmoud->email,
            'phone' => $mahmoud->phone,
            'department_id' => $medicalDept->id,
            'position_id' => $posMedicalRep->id,
            'employment_status' => 'active',
            'hire_date' => '2024-05-10',
        ]);

        AttendanceRecord::updateOrCreate([
            'employee_id' => $empMahmoud->id,
            'attendance_date' => $today,
        ], [
            'check_in' => '08:50:00',
            'status' => 'present',
            'worked_minutes' => 480,
            'source' => 'mobile_gps',
        ]);

        // 5. Medical Representative 3: Dr. Sara Abdelrahman - Giza (Dokki & Mohandessin)
        $saraMr = User::updateOrCreate(['email' => 'sara.abdelrahman@bluezone.com'], [
            'name' => 'Dr. Sara Abdelrahman (MR)',
            'password' => Hash::make('password'),
            'role_id' => $mrRole->id,
            'status' => 'active',
            'phone' => '+201044455667',
            'country_id' => $egypt->id,
            'city_id' => $giza->id,
            'area_id' => $dokkiArea->id,
            'break_id' => $brkMosaddak->id,
        ]);

        $empSaraMr = Employee::updateOrCreate(['user_id' => $saraMr->id], [
            'employee_number' => 'EMP-EG-004',
            'first_name' => 'Sara',
            'last_name' => 'Abdelrahman',
            'email' => $saraMr->email,
            'phone' => $saraMr->phone,
            'department_id' => $medicalDept->id,
            'position_id' => $posMedicalRep->id,
            'employment_status' => 'active',
            'hire_date' => '2024-06-01',
        ]);

        AttendanceRecord::updateOrCreate([
            'employee_id' => $empSaraMr->id,
            'attendance_date' => $today,
        ], [
            'check_in' => '09:00:00',
            'status' => 'present',
            'worked_minutes' => 480,
            'source' => 'mobile_gps',
        ]);

        // 6. Medical Representative 4: Dr. Mohamed El-Sayed - Alexandria (Smouha)
        $mohamedAlex = User::updateOrCreate(['email' => 'mohamed.elsayed@bluezone.com'], [
            'name' => 'Dr. Mohamed El-Sayed (MR)',
            'password' => Hash::make('password'),
            'role_id' => $mrRole->id,
            'status' => 'active',
            'phone' => '+201055566778',
            'country_id' => $egypt->id,
            'city_id' => $alex->id,
            'area_id' => $smouhaArea->id,
            'break_id' => $brkSmouha->id,
        ]);

        $empMohamedAlex = Employee::updateOrCreate(['user_id' => $mohamedAlex->id], [
            'employee_number' => 'EMP-EG-005',
            'first_name' => 'Mohamed',
            'last_name' => 'El-Sayed',
            'email' => $mohamedAlex->email,
            'phone' => $mohamedAlex->phone,
            'department_id' => $medicalDept->id,
            'position_id' => $posMedicalRep->id,
            'employment_status' => 'active',
            'hire_date' => '2024-07-01',
        ]);

        // 7. Warehouse Officer: Eng. Hany Farouk
        $hany = User::updateOrCreate(['email' => 'hany.farouk@bluezone.com'], [
            'name' => 'Eng. Hany Farouk',
            'password' => Hash::make('password'),
            'role_id' => $inventoryRole->id,
            'status' => 'active',
            'phone' => '+201077788990',
            'country_id' => $egypt->id,
            'city_id' => $cairo->id,
            'area_id' => $nasrCityArea->id,
            'break_id' => $brkMakram->id,
        ]);

        $empHany = Employee::updateOrCreate(['user_id' => $hany->id], [
            'employee_number' => 'EMP-EG-006',
            'first_name' => 'Hany',
            'last_name' => 'Farouk',
            'email' => $hany->email,
            'phone' => $hany->phone,
            'department_id' => $supplyDept->id,
            'position_id' => $posWarehouse->id,
            'employment_status' => 'active',
            'hire_date' => '2023-11-01',
        ]);

        // 8. HR Manager: Rania Fouad
        $rania = User::updateOrCreate(['email' => 'rania.fouad@bluezone.com'], [
            'name' => 'Rania Fouad',
            'password' => Hash::make('password'),
            'role_id' => $hrRole->id,
            'status' => 'active',
            'phone' => '+201088899001',
            'country_id' => $egypt->id,
            'city_id' => $cairo->id,
            'area_id' => $heliopolisArea->id,
            'break_id' => $brkKorba->id,
        ]);

        $empRania = Employee::updateOrCreate(['user_id' => $rania->id], [
            'employee_number' => 'EMP-EG-007',
            'first_name' => 'Rania',
            'last_name' => 'Fouad',
            'email' => $rania->email,
            'phone' => $rania->phone,
            'department_id' => $hrDept->id,
            'position_id' => $posHr->id,
            'employment_status' => 'active',
            'hire_date' => '2023-09-15',
        ]);

        echo "=== 5. Seeding Doctor Specialties & Classifications ===\n";
        $specCardio = ContactSpecialty::updateOrCreate(['code' => 'CARDIO'], ['name' => 'Cardiology', 'is_active' => true]);
        $specEndo = ContactSpecialty::updateOrCreate(['code' => 'ENDO'], ['name' => 'Endocrinology & Diabetes', 'is_active' => true]);
        $specNeuro = ContactSpecialty::updateOrCreate(['code' => 'NEURO'], ['name' => 'Neurology & Neurosurgery', 'is_active' => true]);
        $specDerma = ContactSpecialty::updateOrCreate(['code' => 'DERMA'], ['name' => 'Dermatology & Cosmetology', 'is_active' => true]);
        $specOrtho = ContactSpecialty::updateOrCreate(['code' => 'ORTHO'], ['name' => 'Orthopedic Surgery & Trauma', 'is_active' => true]);
        $specPedia = ContactSpecialty::updateOrCreate(['code' => 'PEDIA'], ['name' => 'Pediatrics & Neonatology', 'is_active' => true]);
        $specIntern = ContactSpecialty::updateOrCreate(['code' => 'INTERN'], ['name' => 'Internal Medicine & Gastroenterology', 'is_active' => true]);
        $specGyn = ContactSpecialty::updateOrCreate(['code' => 'GYN'], ['name' => 'Obstetrics & Gynecology', 'is_active' => true]);
        $specOphth = ContactSpecialty::updateOrCreate(['code' => 'OPHTH'], ['name' => 'Ophthalmology & Eye Surgery', 'is_active' => true]);
        $specEnt = ContactSpecialty::updateOrCreate(['code' => 'ENT'], ['name' => 'ENT (Otolaryngology)', 'is_active' => true]);

        $classAPlus = ContactClassification::updateOrCreate(['code' => 'A+'], ['label' => 'Key Opinion Leaders (High Prescribers)', 'required_visits' => 4, 'points' => 400, 'sort_order' => 1, 'is_active' => true]);
        $classA = ContactClassification::updateOrCreate(['code' => 'A'], ['label' => 'Tier 1 Consultants', 'required_visits' => 2, 'points' => 200, 'sort_order' => 2, 'is_active' => true]);
        $classB = ContactClassification::updateOrCreate(['code' => 'B'], ['label' => 'Tier 2 Specialists', 'required_visits' => 1, 'points' => 100, 'sort_order' => 3, 'is_active' => true]);
        $classC = ContactClassification::updateOrCreate(['code' => 'C'], ['label' => 'General Coverage', 'required_visits' => 1, 'points' => 50, 'sort_order' => 4, 'is_active' => true]);

        echo "=== 6. Seeding Egyptian Doctors & Medical Centers ===\n";
        $doctorsData = [
            [
                'code' => 'DOC-EG-001',
                'name' => 'Dr. Ahmed El-Sherif',
                'specialty_id' => $specCardio->id,
                'classification_id' => $classAPlus->id,
                'hospital_clinic_name' => 'Cairo Heart Center & Cleopatra Hospital',
                'country_id' => $egypt->id,
                'city_id' => $cairo->id,
                'area_id' => $nasrCityArea->id,
                'break_id' => $brkAbbas->id,
                'phone' => '+201001122331',
                'email' => 'ahmed.elsherif@cairo-heart.com',
                'address' => '45 Abbas El-Akkad St, Nasr City, Cairo',
                'latitude' => 30.0561,
                'longitude' => 31.3444,
                'target_rep' => $kareem->id,
            ],
            [
                'code' => 'DOC-EG-002',
                'name' => 'Dr. Sarah Mansour',
                'specialty_id' => $specPedia->id,
                'classification_id' => $classAPlus->id,
                'hospital_clinic_name' => 'Cleopatra Hospitals Pediatric Unit',
                'country_id' => $egypt->id,
                'city_id' => $cairo->id,
                'area_id' => $heliopolisArea->id,
                'break_id' => $brkKorba->id,
                'phone' => '+201002233442',
                'email' => 'sarah.mansour@cleopatra-hospitals.com',
                'address' => '12 Baghdad St, El-Korba, Heliopolis, Cairo',
                'latitude' => 30.0871,
                'longitude' => 31.3285,
                'target_rep' => $kareem->id,
            ],
            [
                'code' => 'DOC-EG-003',
                'name' => 'Dr. Tarek El-Kady',
                'specialty_id' => $specOrtho->id,
                'classification_id' => $classA->id,
                'hospital_clinic_name' => 'As-Salam International Hospital, Ortho Wing',
                'country_id' => $egypt->id,
                'city_id' => $cairo->id,
                'area_id' => $maadiArea->id,
                'break_id' => $brkCornicheMaadi->id,
                'phone' => '+201003344553',
                'email' => 'tarek.elkady@assalam-hospital.com',
                'address' => 'Corniche El-Maadi, As-Salam Hospital, Cairo',
                'latitude' => 29.9723,
                'longitude' => 31.2589,
                'target_rep' => $kareem->id,
            ],
            [
                'code' => 'DOC-EG-004',
                'name' => 'Dr. Laila Hosny',
                'specialty_id' => $specDerma->id,
                'classification_id' => $classA->id,
                'hospital_clinic_name' => 'Loran Elite Derma Center',
                'country_id' => $egypt->id,
                'city_id' => $alex->id,
                'area_id' => $loranArea->id,
                'break_id' => $brkLoran->id,
                'phone' => '+201004455664',
                'email' => 'laila.hosny@elitederma-alex.com',
                'address' => '680 Abu Qir Rd, Loran, Alexandria',
                'latitude' => 31.2425,
                'longitude' => 29.9678,
                'target_rep' => $mohamedAlex->id,
            ],
            [
                'code' => 'DOC-EG-005',
                'name' => 'Dr. Hossam El-Din Abdelkader',
                'specialty_id' => $specEndo->id,
                'classification_id' => $classAPlus->id,
                'hospital_clinic_name' => 'Mosaddak Medical Center & Anglo American Hospital',
                'country_id' => $egypt->id,
                'city_id' => $giza->id,
                'area_id' => $dokkiArea->id,
                'break_id' => $brkMosaddak->id,
                'phone' => '+201005566775',
                'email' => 'hossam.abdelkader@anglo-hospital.com',
                'address' => '22 Mosaddak St, Dokki, Giza',
                'latitude' => 30.0385,
                'longitude' => 31.2124,
                'target_rep' => $saraMr->id,
            ],
            [
                'code' => 'DOC-EG-006',
                'name' => 'Dr. Mona El-Gohary',
                'specialty_id' => $specGyn->id,
                'classification_id' => $classAPlus->id,
                'hospital_clinic_name' => 'Shifa Hospital & Medical Park Premier',
                'country_id' => $egypt->id,
                'city_id' => $cairo->id,
                'area_id' => $newCairoArea->id,
                'break_id' => $brkMedPark->id,
                'phone' => '+201006677886',
                'email' => 'mona.elgohary@shifa-hospital.com',
                'address' => 'North 90th St, Medical Park Premier #305, New Cairo',
                'latitude' => 30.0270,
                'longitude' => 31.4913,
                'target_rep' => $mahmoud->id,
            ],
            [
                'code' => 'DOC-EG-007',
                'name' => 'Dr. Amr Mostafa',
                'specialty_id' => $specEnt->id,
                'classification_id' => $classB->id,
                'hospital_clinic_name' => 'Wadi El-Nile Hospital, ENT Dept',
                'country_id' => $egypt->id,
                'city_id' => $giza->id,
                'area_id' => $dokkiArea->id,
                'break_id' => $brkBatal->id,
                'phone' => '+201007788997',
                'email' => 'amr.mostafa@wadielnile.com',
                'address' => 'Batal Ahmed Abdelaziz St, Dokki, Giza',
                'latitude' => 30.0450,
                'longitude' => 31.2100,
                'target_rep' => $saraMr->id,
            ],
            [
                'code' => 'DOC-EG-008',
                'name' => 'Dr. Rasha Fahmy',
                'specialty_id' => $specOphth->id,
                'classification_id' => $classA->id,
                'hospital_clinic_name' => 'International Eye Hospital, Arkan Plaza',
                'country_id' => $egypt->id,
                'city_id' => $giza->id,
                'area_id' => $zayedArea->id,
                'break_id' => $brkArkan->id,
                'phone' => '+201008899008',
                'email' => 'rasha.fahmy@eyehospital-eg.com',
                'address' => 'Arkan Plaza Tower 3, Sheikh Zayed, Giza',
                'latitude' => 30.0150,
                'longitude' => 30.9850,
                'target_rep' => $saraMr->id,
            ],
            [
                'code' => 'DOC-EG-009',
                'name' => 'Dr. Hany Shaker',
                'specialty_id' => $specNeuro->id,
                'classification_id' => $classA->id,
                'hospital_clinic_name' => 'Ain Shams Specialized Hospital Neuro Unit',
                'country_id' => $egypt->id,
                'city_id' => $cairo->id,
                'area_id' => $nasrCityArea->id,
                'break_id' => $brkMakram->id,
                'phone' => '+201009900119',
                'email' => 'hany.shaker@ainshams-neuro.med.eg',
                'address' => 'Makram Ebeid St, Nasr City, Cairo',
                'latitude' => 30.0610,
                'longitude' => 31.3490,
                'target_rep' => $kareem->id,
            ],
            [
                'code' => 'DOC-EG-010',
                'name' => 'Dr. Ibrahim Shawky',
                'specialty_id' => $specIntern->id,
                'classification_id' => $classB->id,
                'hospital_clinic_name' => 'Smouha Specialized Medical Center',
                'country_id' => $egypt->id,
                'city_id' => $alex->id,
                'area_id' => $smouhaArea->id,
                'break_id' => $brkSmouha->id,
                'phone' => '+201010011220',
                'email' => 'ibrahim.shawky@smouha-med.com',
                'address' => 'Victor Emmanuel St, Smouha, Alexandria',
                'latitude' => 31.2150,
                'longitude' => 29.9400,
                'target_rep' => $mohamedAlex->id,
            ],
            [
                'code' => 'DOC-EG-011',
                'name' => 'Dr. Khaled Badawi',
                'specialty_id' => $specIntern->id,
                'classification_id' => $classA->id,
                'hospital_clinic_name' => 'Air Force Specialized Hospital & Roxy Polyclinic',
                'country_id' => $egypt->id,
                'city_id' => $cairo->id,
                'area_id' => $heliopolisArea->id,
                'break_id' => $brkRoxy->id,
                'phone' => '+201011122331',
                'email' => 'khaled.badawi@airforce-med.eg',
                'address' => 'Roxy Square, Heliopolis, Cairo',
                'latitude' => 30.0920,
                'longitude' => 31.3150,
                'target_rep' => $kareem->id,
            ],
            [
                'code' => 'DOC-EG-012',
                'name' => 'Dr. Nourhan Essam',
                'specialty_id' => $specIntern->id,
                'classification_id' => $classB->id,
                'hospital_clinic_name' => 'Degla Medical Center Suites',
                'country_id' => $egypt->id,
                'city_id' => $cairo->id,
                'area_id' => $maadiArea->id,
                'break_id' => $brkDegla->id,
                'phone' => '+201012233442',
                'email' => 'nourhan.essam@deglamedical.com',
                'address' => 'St 233, Degla Maadi, Cairo',
                'latitude' => 29.9600,
                'longitude' => 31.2750,
                'target_rep' => $mahmoud->id,
            ],
        ];

        $createdDoctors = [];
        foreach ($doctorsData as $d) {
            $repId = $d['target_rep'];
            unset($d['target_rep']);
            $doc = Contact::updateOrCreate(['code' => $d['code']], $d);
            $doc->is_active = true;
            $doc->save();
            $createdDoctors[] = ['doctor' => $doc, 'rep_id' => $repId];
        }

        echo "=== 7. Seeding Active Visit Cycle & Assignments ===\n";
        $cycle = VisitCycle::updateOrCreate(['code' => 'CYCLE-2026-09'], [
            'name' => 'September 2026 Egyptian Field Cycle',
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-30',
            'status' => 'active',
            'notes' => 'Q3 territory cycle covering Cairo, Giza, and Alexandria Key Opinion Leaders.',
        ]);

        foreach ($createdDoctors as $item) {
            $doc = $item['doctor'];
            $repId = $item['rep_id'];
            $targetVisits = $doc->classification?->code === 'A+' ? 4 : ($doc->classification?->code === 'A' ? 2 : 1);

            ContactAssignment::updateOrCreate([
                'cycle_id' => $cycle->id,
                'mr_id' => $repId,
                'contact_id' => $doc->id,
            ], [
                'target_visits' => $targetVisits,
                'visits_done' => 1,
                'achieved_points' => 100,
                'target_points' => $targetVisits * 100,
                'is_active' => true,
            ]);
        }

        echo "=== 8. Seeding Scheduled & Executed Visits for Egyptian Doctors ===\n";
        $kareemDocs = array_filter($createdDoctors, fn($x) => $x['rep_id'] == $kareem->id);
        $kareemDocList = array_map(fn($x) => $x['doctor'], array_values($kareemDocs));

        // Schedule 1: Completed Visit for Dr. Ahmed El-Sherif (Cairo Heart Center)
        if (isset($kareemDocList[0])) {
            $sched1 = ScheduledVisit::updateOrCreate([
                'mr_id' => $kareem->id,
                'contact_id' => $kareemDocList[0]->id,
                'scheduled_at' => '2026-09-26 09:30:00',
            ], [
                'cycle_id' => $cycle->id,
                'status' => 'completed',
                'notes' => 'Presentation on Cardiox and cellular heart wellness formulations.',
            ]);

            Visit::updateOrCreate(['scheduled_visit_id' => $sched1->id], [
                'mr_id' => $kareem->id,
                'contact_id' => $kareemDocList[0]->id,
                'cycle_id' => $cycle->id,
                'checkin_at' => '2026-09-26 09:28:15',
                'checkout_at' => '2026-09-26 09:55:40',
                'checkin_lat' => 30.0561,
                'checkin_lng' => 31.3444,
                'gps_verified' => true,
                'duration_minutes' => 27,
                'outcome' => 'positive',
                'notes' => 'Dr. Ahmed El-Sherif welcomed Cardiox clinical data. Requested 10 sample packs for clinic patients.',
            ]);
        }

        // Schedule 2: In-Progress Visit for Dr. Sarah Mansour (Cleopatra Hospitals)
        if (isset($kareemDocList[1])) {
            ScheduledVisit::updateOrCreate([
                'mr_id' => $kareem->id,
                'contact_id' => $kareemDocList[1]->id,
                'scheduled_at' => '2026-09-26 11:15:00',
            ], [
                'cycle_id' => $cycle->id,
                'status' => 'in_progress',
                'notes' => 'Detailing on pediatric immunity supplements and tolerance study.',
            ]);
        }

        // Schedule 3: Planned Visit for Dr. Tarek El-Kady (As-Salam Int Hospital)
        if (isset($kareemDocList[2])) {
            ScheduledVisit::updateOrCreate([
                'mr_id' => $kareem->id,
                'contact_id' => $kareemDocList[2]->id,
                'scheduled_at' => '2026-09-26 13:30:00',
            ], [
                'cycle_id' => $cycle->id,
                'status' => 'planned',
                'notes' => 'Review joint mobility clinical trial brochure with orthopedic department.',
            ]);
        }

        // Schedule 4: Planned Visit for Dr. Khaled Badawi (Roxy)
        if (isset($kareemDocList[4])) {
            ScheduledVisit::updateOrCreate([
                'mr_id' => $kareem->id,
                'contact_id' => $kareemDocList[4]->id,
                'scheduled_at' => '2026-09-26 15:00:00',
            ], [
                'cycle_id' => $cycle->id,
                'status' => 'planned',
                'notes' => 'Follow up on outpatient respiratory prescription feedback.',
            ]);
        }

        // Giza Visits for Sara MR
        $saraDocs = array_filter($createdDoctors, fn($x) => $x['rep_id'] == $saraMr->id);
        $saraDocList = array_map(fn($x) => $x['doctor'], array_values($saraDocs));

        if (isset($saraDocList[0])) {
            $schedSara1 = ScheduledVisit::updateOrCreate([
                'mr_id' => $saraMr->id,
                'contact_id' => $saraDocList[0]->id,
                'scheduled_at' => '2026-09-26 10:00:00',
            ], [
                'cycle_id' => $cycle->id,
                'status' => 'completed',
                'notes' => 'Met Dr. Hossam El-Din Abdelkader at Mosaddak Medical Center.',
            ]);

            Visit::updateOrCreate(['scheduled_visit_id' => $schedSara1->id], [
                'mr_id' => $saraMr->id,
                'contact_id' => $saraDocList[0]->id,
                'cycle_id' => $cycle->id,
                'checkin_at' => '2026-09-26 09:58:00',
                'checkout_at' => '2026-09-26 10:28:00',
                'checkin_lat' => 30.0385,
                'checkin_lng' => 31.2124,
                'gps_verified' => true,
                'duration_minutes' => 30,
                'outcome' => 'positive',
                'notes' => 'Dr. Hossam confirmed prescribing ImmunoMax Zinc for diabetic patients with slow healing.',
            ]);
        }

        echo "=== EGYPTIAN DUMMY DATA SEEDING COMPLETED SUCCESSFULLY! ===\n";
    }
}
