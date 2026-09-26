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
use App\Models\Product;
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
            'slug' => 'super_admin',
            'description' => 'Unrestricted root authority across all application modules and settings.',
            'permissions' => ['*'],
        ]);

        // 2. MR Line Manager
        $mrLineManagerRole = Role::updateOrCreate(['id' => 2], [
            'name' => 'MR Line Manager',
            'slug' => 'mr_line_manager',
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
            'slug' => 'mr',
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
                'hr_attendance.create' => true, // Allows personal punch in/out
            ],
        ]);

        // 4. Warehouse & Inventory Officer
        $inventoryRole = Role::updateOrCreate(['id' => 6], [
            'name' => 'Inventory Officer',
            'slug' => 'inventory_staff',
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
            'slug' => 'hr_manager',
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
            'slug' => 'commercial_sales',
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

        // Clean up Role 4 (Manager) if exists
        $mgrRole = Role::find(4);
        if ($mgrRole) {
            $mgrRole->update([
                'name' => 'Operations Manager',
                'slug' => 'operations_manager',
                'permissions' => ['products.*', 'orders.*', 'customers.*', 'inventory.*', 'reports.view'],
            ]);
        }

        echo "=== 2. Territories, Countries, and Cities ===\n";
        $country = Country::firstOrCreate(['iso2' => 'SA'], [
            'name' => 'Saudi Arabia',
            'name_ar' => 'المملكة العربية السعودية',
            'phone_code' => '966',
            'currency_code' => 'SAR',
            'is_active' => true,
        ]);

        $riyadh = City::firstOrCreate(['country_id' => $country->id, 'name_en' => 'Riyadh'], [
            'name_ar' => 'الرياض',
            'is_active' => true,
        ]);

        $centralArea = Area::firstOrCreate(['city_id' => $riyadh->id, 'name_en' => 'Central Riyadh Territory'], [
            'country_id' => $country->id,
            'name_ar' => 'قطاع وسط الرياض',
            'code' => 'RUH-C',
            'is_active' => true,
        ]);

        $northArea = Area::firstOrCreate(['city_id' => $riyadh->id, 'name_en' => 'North Riyadh Territory'], [
            'country_id' => $country->id,
            'name_ar' => 'قطاع شمال الرياض',
            'code' => 'RUH-N',
            'is_active' => true,
        ]);

        $eastArea = Area::firstOrCreate(['city_id' => $riyadh->id, 'name_en' => 'East Riyadh Territory'], [
            'country_id' => $country->id,
            'name_ar' => 'قطاع شرق الرياض',
            'code' => 'RUH-E',
            'is_active' => true,
        ]);

        echo "=== 3. Departments and Job Positions ===\n";
        $medicalDept = Department::firstOrCreate(['name_en' => 'Medical Field Operations'], [
            'name_ar' => 'العمليات الميدانية والدعاية الطبية',
            'code' => 'MED-OPS',
            'is_active' => true,
        ]);
        $hrDept = Department::firstOrCreate(['name_en' => 'Human Resources'], [
            'name_ar' => 'الموارد البشرية والشؤون الإدارية',
            'code' => 'HR',
            'is_active' => true,
        ]);
        $supplyDept = Department::firstOrCreate(['name_en' => 'Warehouse & Supply Chain'], [
            'name_ar' => 'المستودعات وسلاسل الإمداد',
            'code' => 'SCM',
            'is_active' => true,
        ]);

        $posLineManager = Position::firstOrCreate(['code' => 'POS-MRLM'], [
            'name_en' => 'MR Line Manager & Field Supervisor',
            'name_ar' => 'مشرف ومسؤول مناديب الدعاية الطبية',
            'department_id' => $medicalDept->id,
            'is_active' => true,
        ]);
        $posMedicalRep = Position::firstOrCreate(['code' => 'POS-MR'], [
            'name_en' => 'Senior Medical Representative',
            'name_ar' => 'مندوب دعاية طبية أول',
            'department_id' => $medicalDept->id,
            'is_active' => true,
        ]);
        $posWarehouse = Position::firstOrCreate(['code' => 'POS-WH'], [
            'name_en' => 'Warehouse & Inventory Officer',
            'name_ar' => 'مسؤول المستودعات والمخزون',
            'department_id' => $supplyDept->id,
            'is_active' => true,
        ]);
        $posHr = Position::firstOrCreate(['code' => 'POS-HR'], [
            'name_en' => 'HR & Workforce Manager',
            'name_ar' => 'مدير شؤون الموظفين والموارد البشرية',
            'department_id' => $hrDept->id,
            'is_active' => true,
        ]);

        echo "=== 4. Seeding Real Staff Users & Employee Profiles ===\n";
        $today = Carbon::today();

        // 1. Super Admin: Tariq M.
        $superAdmin = User::updateOrCreate(['id' => 10], [
            'name' => 'Tariq M.',
            'email' => 'tariq@bluezone.com',
            'password' => Hash::make('password'),
            'role_id' => $superAdminRole->id,
            'status' => 'active',
            'phone' => '+966500000001',
            'country_id' => $country->id,
            'city_id' => $riyadh->id,
        ]);

        // 2. MR Line Manager: Dr. Yasser Mansour
        $lineManager = User::updateOrCreate(['email' => 'yasser.mansour@bluezone.com'], [
            'name' => 'Dr. Yasser Mansour',
            'password' => Hash::make('password'),
            'role_id' => $mrLineManagerRole->id,
            'status' => 'active',
            'phone' => '+966501112233',
            'country_id' => $country->id,
            'city_id' => $riyadh->id,
            'area_id' => $centralArea->id,
        ]);

        $empLineMgr = Employee::updateOrCreate(['user_id' => $lineManager->id], [
            'employee_number' => 'EMP-001',
            'first_name' => 'Yasser',
            'last_name' => 'Mansour',
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

        // 3. Medical Representative 1: Dr. Kareem Tarek (User ID 9)
        $kareem = User::updateOrCreate(['id' => 9], [
            'name' => 'Dr. Kareem Tarek (MR)',
            'email' => 'kareem.tarek@bluezone.com',
            'password' => Hash::make('password'),
            'role_id' => $mrRole->id,
            'status' => 'active',
            'phone' => '+966551234567',
            'country_id' => $country->id,
            'city_id' => $riyadh->id,
            'area_id' => $centralArea->id,
        ]);

        // 3. Medical Representative 1: Dr. Kareem Tarek (User ID 9)
        $kareem = User::updateOrCreate(['id' => 9], [
            'name' => 'Dr. Kareem Tarek (MR)',
            'email' => 'kareem.tarek@bluezone.com',
            'password' => Hash::make('password'),
            'role_id' => $mrRole->id,
            'status' => 'active',
            'phone' => '+966551234567',
            'country_id' => $country->id,
            'city_id' => $riyadh->id,
            'area_id' => $centralArea->id,
        ]);

        $empKareem = Employee::updateOrCreate(['user_id' => $kareem->id], [
            'employee_number' => 'EMP-002',
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

        // 4. Medical Representative 2: Dr. Nourhan Ali
        $nourhan = User::updateOrCreate(['email' => 'nourhan.ali@bluezone.com'], [
            'name' => 'Dr. Nourhan Ali (MR)',
            'password' => Hash::make('password'),
            'role_id' => $mrRole->id,
            'status' => 'active',
            'phone' => '+966552345678',
            'country_id' => $country->id,
            'city_id' => $riyadh->id,
            'area_id' => $northArea->id,
        ]);

        $empNourhan = Employee::updateOrCreate(['user_id' => $nourhan->id], [
            'employee_number' => 'EMP-003',
            'first_name' => 'Nourhan',
            'last_name' => 'Ali',
            'email' => $nourhan->email,
            'phone' => $nourhan->phone,
            'department_id' => $medicalDept->id,
            'position_id' => $posMedicalRep->id,
            'employment_status' => 'active',
            'hire_date' => '2024-05-10',
        ]);

        AttendanceRecord::updateOrCreate([
            'employee_id' => $empNourhan->id,
            'attendance_date' => $today,
        ], [
            'check_in' => '08:50:00',
            'status' => 'present',
            'worked_minutes' => 480,
            'source' => 'mobile_gps',
        ]);

        // 5. Medical Representative 3: Dr. Omar Farouk
        $omar = User::updateOrCreate(['email' => 'omar.farouk@bluezone.com'], [
            'name' => 'Dr. Omar Farouk (MR)',
            'password' => Hash::make('password'),
            'role_id' => $mrRole->id,
            'status' => 'active',
            'phone' => '+966553456789',
            'country_id' => $country->id,
            'city_id' => $riyadh->id,
            'area_id' => $eastArea->id,
        ]);

        $empOmar = Employee::updateOrCreate(['user_id' => $omar->id], [
            'employee_number' => 'EMP-004',
            'first_name' => 'Omar',
            'last_name' => 'Farouk',
            'email' => $omar->email,
            'phone' => $omar->phone,
            'department_id' => $medicalDept->id,
            'position_id' => $posMedicalRep->id,
            'employment_status' => 'active',
            'hire_date' => '2024-06-01',
        ]);

        AttendanceRecord::updateOrCreate([
            'employee_id' => $empOmar->id,
            'attendance_date' => $today,
        ], [
            'check_in' => '09:00:00',
            'status' => 'present',
            'worked_minutes' => 480,
            'source' => 'mobile_gps',
        ]);

        // 6. Warehouse & Inventory Officer: Eng. Khaled Al-Ghamdi
        $khaled = User::updateOrCreate(['email' => 'khaled.ghamdi@bluezone.com'], [
            'name' => 'Eng. Khaled Al-Ghamdi',
            'password' => Hash::make('password'),
            'role_id' => $inventoryRole->id,
            'status' => 'active',
            'phone' => '+966504567890',
            'country_id' => $country->id,
            'city_id' => $riyadh->id,
        ]);

        $empKhaled = Employee::updateOrCreate(['user_id' => $khaled->id], [
            'employee_number' => 'EMP-005',
            'first_name' => 'Khaled',
            'last_name' => 'Al-Ghamdi',
            'email' => $khaled->email,
            'phone' => $khaled->phone,
            'department_id' => $supplyDept->id,
            'position_id' => $posWarehouse->id,
            'employment_status' => 'active',
            'hire_date' => '2023-11-01',
        ]);

        // 7. HR Manager: Sara Al-Zahrani
        $sara = User::updateOrCreate(['email' => 'sara.zahrani@bluezone.com'], [
            'name' => 'Sara Al-Zahrani',
            'password' => Hash::make('password'),
            'role_id' => $hrRole->id,
            'status' => 'active',
            'phone' => '+966505678901',
            'country_id' => $country->id,
            'city_id' => $riyadh->id,
        ]);

        $empSara = Employee::updateOrCreate(['user_id' => $sara->id], [
            'employee_number' => 'EMP-006',
            'first_name' => 'Sara',
            'last_name' => 'Al-Zahrani',
            'email' => $sara->email,
            'phone' => $sara->phone,
            'department_id' => $hrDept->id,
            'position_id' => $posHr->id,
            'employment_status' => 'active',
            'hire_date' => '2023-09-15',
        ]);

        echo "=== 5. Seeding Real Doctor Specialties & Classifications ===\n";
        $specCardio = ContactSpecialty::firstOrCreate(['code' => 'CARDIO'], ['name' => 'Cardiology', 'is_active' => true]);
        $specEndo = ContactSpecialty::firstOrCreate(['code' => 'ENDO'], ['name' => 'Endocrinology & Diabetes', 'is_active' => true]);
        $specNeuro = ContactSpecialty::firstOrCreate(['code' => 'NEURO'], ['name' => 'Neurology', 'is_active' => true]);
        $specDerma = ContactSpecialty::firstOrCreate(['code' => 'DERMA'], ['name' => 'Dermatology & Cosmetology', 'is_active' => true]);
        $specOrtho = ContactSpecialty::firstOrCreate(['code' => 'ORTHO'], ['name' => 'Orthopedic Surgery', 'is_active' => true]);
        $specPedia = ContactSpecialty::firstOrCreate(['code' => 'PEDIA'], ['name' => 'Pediatrics', 'is_active' => true]);
        $specIntern = ContactSpecialty::firstOrCreate(['code' => 'INTERN'], ['name' => 'Internal Medicine', 'is_active' => true]);

        $classAPlus = ContactClassification::firstOrCreate(['code' => 'A+'], ['label' => 'Key Opinion Leaders (High Prescribers)', 'required_visits' => 4, 'points' => 400, 'sort_order' => 1, 'is_active' => true]);
        $classA = ContactClassification::firstOrCreate(['code' => 'A'], ['label' => 'Tier 1 Consultants', 'required_visits' => 2, 'points' => 200, 'sort_order' => 2, 'is_active' => true]);
        $classB = ContactClassification::firstOrCreate(['code' => 'B'], ['label' => 'Tier 2 Specialists', 'required_visits' => 1, 'points' => 100, 'sort_order' => 3, 'is_active' => true]);
        $classC = ContactClassification::firstOrCreate(['code' => 'C'], ['label' => 'General Coverage', 'required_visits' => 1, 'points' => 50, 'sort_order' => 4, 'is_active' => true]);

        echo "=== 6. Seeding Real Doctors & Medical Clinics ===\n";
        $doctorsData = [
            [
                'code' => 'DOC-001',
                'name' => 'Dr. Ahmed El-Sherif',
                'specialty_id' => $specCardio->id,
                'classification_id' => $classAPlus->id,
                'hospital_clinic_name' => 'Al-Salam International Hospital, Clinic #402',
                'city_id' => $riyadh->id,
                'area_id' => $centralArea->id,
                'phone' => '+966500112233',
                'email' => 'ahmed.elsherif@alsalam-hospital.med.sa',
                'address' => 'King Fahd Road, Central Riyadh',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
                'target_rep' => $kareem->id,
            ],
            [
                'code' => 'DOC-002',
                'name' => 'Dr. Sarah Mansour',
                'specialty_id' => $specEndo->id,
                'classification_id' => $classA->id,
                'hospital_clinic_name' => 'Kingdom Medical Center, Tower B',
                'city_id' => $riyadh->id,
                'area_id' => $centralArea->id,
                'phone' => '+966500223344',
                'email' => 'sarah.mansour@kingdom-med.sa',
                'address' => 'Olaya St, Central Riyadh',
                'latitude' => 24.7180,
                'longitude' => 46.6710,
                'target_rep' => $kareem->id,
            ],
            [
                'code' => 'DOC-003',
                'name' => 'Dr. Mohamed Nabil',
                'specialty_id' => $specNeuro->id,
                'classification_id' => $classB->id,
                'hospital_clinic_name' => 'Al-Amal Specialized Neuro Clinic',
                'city_id' => $riyadh->id,
                'area_id' => $centralArea->id,
                'phone' => '+966500334455',
                'email' => 'm.nabil@neuro-clinic.com',
                'address' => 'Takhassusi St, Central Riyadh',
                'latitude' => 24.7090,
                'longitude' => 46.6800,
                'target_rep' => $kareem->id,
            ],
            [
                'code' => 'DOC-004',
                'name' => 'Dr. Laila Hassan',
                'specialty_id' => $specDerma->id,
                'classification_id' => $classA->id,
                'hospital_clinic_name' => 'Elite Dermatology Center, Suite 10',
                'city_id' => $riyadh->id,
                'area_id' => $centralArea->id,
                'phone' => '+966500445566',
                'email' => 'laila.hassan@elitederma.sa',
                'address' => 'Uruba St, Central Riyadh',
                'latitude' => 24.7210,
                'longitude' => 46.6690,
                'target_rep' => $kareem->id,
            ],
            [
                'code' => 'DOC-005',
                'name' => 'Dr. Zaid Al-Harbi',
                'specialty_id' => $specOrtho->id,
                'classification_id' => $classAPlus->id,
                'hospital_clinic_name' => 'Riyadh Care Hospital, Ortho Wing',
                'city_id' => $riyadh->id,
                'area_id' => $northArea->id,
                'phone' => '+966500556677',
                'email' => 'zaid.alharbi@riyadhcare.med.sa',
                'address' => 'King Abdullah Road, North Riyadh',
                'latitude' => 24.7500,
                'longitude' => 46.6800,
                'target_rep' => $nourhan->id,
            ],
            [
                'code' => 'DOC-006',
                'name' => 'Dr. Mona Al-Qasimi',
                'specialty_id' => $specPedia->id,
                'classification_id' => $classA->id,
                'hospital_clinic_name' => 'Al-Rahma Children Hospital',
                'city_id' => $riyadh->id,
                'area_id' => $northArea->id,
                'phone' => '+966500667788',
                'email' => 'mona.qasimi@alrahma.med.sa',
                'address' => 'Northern Ring Rd, North Riyadh',
                'latitude' => 24.7600,
                'longitude' => 46.6500,
                'target_rep' => $nourhan->id,
            ],
            [
                'code' => 'DOC-007',
                'name' => 'Dr. Fahad Al-Otaibi',
                'specialty_id' => $specIntern->id,
                'classification_id' => $classA->id,
                'hospital_clinic_name' => 'Al-Hayat General Hospital',
                'city_id' => $riyadh->id,
                'area_id' => $northArea->id,
                'phone' => '+966500778899',
                'email' => 'fahad.otaibi@alhayat.med.sa',
                'address' => 'Imam Saud Road, North Riyadh',
                'latitude' => 24.7450,
                'longitude' => 46.6900,
                'target_rep' => $nourhan->id,
            ],
            [
                'code' => 'DOC-008',
                'name' => 'Dr. Hoda Sulaiman',
                'specialty_id' => $specIntern->id,
                'classification_id' => $classB->id,
                'hospital_clinic_name' => 'Sulaiman Specialized Clinics',
                'city_id' => $riyadh->id,
                'area_id' => $eastArea->id,
                'phone' => '+966500889900',
                'email' => 'hoda.sulaiman@sulaiman-clinics.sa',
                'address' => 'Eastern Ring Rd, East Riyadh',
                'latitude' => 24.7300,
                'longitude' => 46.7500,
                'target_rep' => $omar->id,
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
            'name' => 'September 2026 Field Cycle',
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-30',
            'status' => 'active',
            'notes' => 'Q3 territory cycle focused on Blue Mind and Blue Defense physician adoption.',
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

        echo "=== 8. Seeding Scheduled & Executed Visits for MRs ===\n";
        $kareemDocs = array_filter($createdDoctors, fn($x) => $x['rep_id'] == $kareem->id);
        $kareemDocList = array_map(fn($x) => $x['doctor'], array_values($kareemDocs));

        // Schedule 1: Today 09:30 AM (Ahmed El-Sherif)
        if (isset($kareemDocList[0])) {
            $sched1 = ScheduledVisit::updateOrCreate([
                'mr_id' => $kareem->id,
                'contact_id' => $kareemDocList[0]->id,
                'scheduled_at' => '2026-09-26 09:30:00',
            ], [
                'cycle_id' => $cycle->id,
                'status' => 'completed',
                'notes' => 'Detailing on Blue Mind 500mg indications and clinical study findings.',
            ]);

            Visit::updateOrCreate(['scheduled_visit_id' => $sched1->id], [
                'mr_id' => $kareem->id,
                'contact_id' => $kareemDocList[0]->id,
                'cycle_id' => $cycle->id,
                'checkin_at' => '2026-09-26 09:28:15',
                'checkout_at' => '2026-09-26 09:52:40',
                'checkin_lat' => 24.7136,
                'checkin_lng' => 46.6753,
                'gps_verified' => true,
                'duration_minutes' => 24,
                'outcome' => 'positive',
                'notes' => 'Dr. Ahmed praised patient tolerance of Blue Mind. Requested 5 sample boxes for hospital outpatient trial.',
            ]);
        }

        // Schedule 2: Today 11:15 AM (Sarah Mansour)
        if (isset($kareemDocList[1])) {
            ScheduledVisit::updateOrCreate([
                'mr_id' => $kareem->id,
                'contact_id' => $kareemDocList[1]->id,
                'scheduled_at' => '2026-09-26 11:15:00',
            ], [
                'cycle_id' => $cycle->id,
                'status' => 'in_progress',
                'notes' => 'Discuss Blue Defense immune support with diabetic patients.',
            ]);
        }

        // Schedule 3: Today 01:30 PM (Laila Hassan)
        if (isset($kareemDocList[3])) {
            ScheduledVisit::updateOrCreate([
                'mr_id' => $kareem->id,
                'contact_id' => $kareemDocList[3]->id,
                'scheduled_at' => '2026-09-26 13:30:00',
            ], [
                'cycle_id' => $cycle->id,
                'status' => 'planned',
                'notes' => 'Deliver promotional brochures for cellular health formulations.',
            ]);
        }

        // Past Executed Visit for Kareem
        if (isset($kareemDocList[2])) {
            Visit::updateOrCreate([
                'mr_id' => $kareem->id,
                'contact_id' => $kareemDocList[2]->id,
                'checkin_at' => '2026-09-24 10:15:00',
            ], [
                'cycle_id' => $cycle->id,
                'checkout_at' => '2026-09-24 10:45:00',
                'checkin_lat' => 24.7090,
                'checkin_lng' => 46.6800,
                'gps_verified' => true,
                'duration_minutes' => 30,
                'outcome' => 'positive',
                'notes' => 'Dr. Mohamed Nabil agreed to include Blue Mind in cognitive support protocols.',
            ]);
        }

        echo "=== FRESH DATA SEEDING COMPLETE WITH SUCCESS! ===\n";
    }
}
