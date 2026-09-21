<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\Mr\Contact;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\ContactClassification;
use App\Models\Mr\ContactSpecialty;
use App\Models\Mr\RepPerformanceSnapshot;
use App\Models\Mr\Visit;
use App\Models\Mr\VisitCycle;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\ContactClassificationSeeder;
use Database\Seeders\ContactSpecialtySeeder;
use Database\Seeders\MrGpsConfigSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class MrAllTablesExcelExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $mr;
    protected VisitCycle $cycle;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->seed([
            ContactClassificationSeeder::class,
            ContactSpecialtySeeder::class,
            MrGpsConfigSeeder::class,
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $mrRole = Role::firstOrCreate(['name' => 'mr']);

        $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
        $this->mr = User::factory()->create(['role_id' => $mrRole->id, 'name' => 'Dr. Tarek']);

        $this->cycle = VisitCycle::create([
            'name' => 'September 2026 Cycle',
            'code' => 'CYCLE-2026-09',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'status' => 'active',
        ]);
    }

    public function test_contacts_table_excel_export_with_autofilter(): void
    {
        $classA = ContactClassification::where('code', 'A')->first();
        $specialty = ContactSpecialty::first();

        Contact::create([
            'code' => 'DOC-001',
            'name' => 'Dr. Ahmed Samy',
            'classification_id' => $classA->id,
            'specialty_id' => $specialty->id,
            'status' => 'active',
            'region' => 'Nasr City',
            'hospital_clinic_name' => 'Al-Salam Hospital',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.mr.contacts.index', ['export' => 'xlsx']));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('Content-Type'));

        $temp = tempnam(sys_get_temp_dir(), 'test_exp_') . '.xlsx';
        file_put_contents($temp, $response->streamedContent());
        $spreadsheet = IOFactory::load($temp);
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertStringContainsString('DOCTOR PORTFOLIO', $sheet->getCell('A1')->getValue());
        $this->assertNotEmpty($sheet->getAutoFilter()->getRange(), 'AutoFilter must be active');
        $this->assertStringStartsWith('A6:', $sheet->getAutoFilter()->getRange());
        $this->assertEquals('DOC-001', $sheet->getCell('A7')->getValue());
        $this->assertEquals('Dr. Ahmed Samy', $sheet->getCell('B7')->getValue());

        @unlink($temp);
    }

    public function test_visits_table_excel_export_with_autofilter(): void
    {
        $contact = Contact::create([
            'code' => 'DOC-002',
            'name' => 'Dr. Mona Zaki',
            'classification_id' => ContactClassification::first()->id,
            'specialty_id' => ContactSpecialty::first()->id,
            'status' => 'active',
        ]);

        Visit::create([
            'mr_id' => $this->mr->id,
            'contact_id' => $contact->id,
            'cycle_id' => $this->cycle->id,
            'checkin_at' => now()->subHour(),
            'checkout_at' => now(),
            'duration_minutes' => 60,
            'gps_verified' => true,
            'distance_from_contact_m' => 45,
            'outcome' => 'successful',
            'notes' => 'Discussed CardioCard',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.mr.visits.index', ['export' => 'xlsx']));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('Content-Type'));

        $temp = tempnam(sys_get_temp_dir(), 'test_exp_') . '.xlsx';
        file_put_contents($temp, $response->streamedContent());
        $spreadsheet = IOFactory::load($temp);
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertStringContainsString('EXECUTED FIELD VISITS', $sheet->getCell('A1')->getValue());
        $this->assertNotEmpty($sheet->getAutoFilter()->getRange(), 'AutoFilter must be active');
        $this->assertEquals('Verified', $sheet->getCell('K7')->getValue());
        $this->assertEquals('Successful', $sheet->getCell('M7')->getValue());

        @unlink($temp);
    }

    public function test_assignments_table_excel_export_with_autofilter(): void
    {
        $contact = Contact::create([
            'code' => 'DOC-003',
            'name' => 'Dr. Khaled Said',
            'classification_id' => ContactClassification::first()->id,
            'specialty_id' => ContactSpecialty::first()->id,
            'status' => 'active',
        ]);

        ContactAssignment::create([
            'cycle_id' => $this->cycle->id,
            'mr_id' => $this->mr->id,
            'contact_id' => $contact->id,
            'target_visits' => 3,
            'visits_done' => 2,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.mr.assignments.index', ['export' => 'xlsx']));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('Content-Type'));

        $temp = tempnam(sys_get_temp_dir(), 'test_exp_') . '.xlsx';
        file_put_contents($temp, $response->streamedContent());
        $spreadsheet = IOFactory::load($temp);
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertStringContainsString('ASSIGNMENTS', $sheet->getCell('A1')->getValue());
        $this->assertNotEmpty($sheet->getAutoFilter()->getRange(), 'AutoFilter must be active');
        $this->assertEquals('DOC-003', $sheet->getCell('D7')->getValue());

        @unlink($temp);
    }

    public function test_performance_scorecard_excel_export_with_autofilter(): void
    {
        RepPerformanceSnapshot::create([
            'mr_id' => $this->mr->id,
            'cycle_id' => $this->cycle->id,
            'coverage_rate_pct' => 88.5,
            'visits_done' => 15,
            'planned_visits' => 18,
            'gps_accuracy_pct' => 95.0,
            'visit_compliance_pct' => 83.3,
            'target_points' => 40,
            'achieved_points' => 35,
            'points_achieved_pct' => 87.5,
            'unreported_days_count' => 1,
            'calculated_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.mr.reports.performance', [
                'cycle_id' => $this->cycle->id,
                'export' => 'xlsx',
            ]));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('Content-Type'));

        $temp = tempnam(sys_get_temp_dir(), 'test_exp_') . '.xlsx';
        file_put_contents($temp, $response->streamedContent());
        $spreadsheet = IOFactory::load($temp);
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertStringContainsString('REP PERFORMANCE SCORECARD', $sheet->getCell('A1')->getValue());
        $this->assertNotEmpty($sheet->getAutoFilter()->getRange(), 'AutoFilter must be active');

        @unlink($temp);
    }

    public function test_cycles_table_excel_export(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.mr.cycles.index', ['export' => 'xlsx']));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('Content-Type'));
    }

    public function test_classifications_table_excel_export(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.mr.classifications.index', ['export' => 'xlsx']));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('Content-Type'));
    }

    public function test_specialties_table_excel_export(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.mr.specialties.index', ['export' => 'xlsx']));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('Content-Type'));
    }
}
