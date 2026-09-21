<?php

namespace Tests\Feature;

use App\Models\Mr\Contact;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\ContactClassification;
use App\Models\Mr\ContactSpecialty;
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

class DoctorCoverageExcelExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->seed([
            ContactClassificationSeeder::class,
            ContactSpecialtySeeder::class,
            MrGpsConfigSeeder::class,
        ]);
    }

    public function test_coverage_report_excel_export_structure_and_autofilter(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $mrRole = Role::firstOrCreate(['name' => 'mr']);

        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $mr = User::factory()->create(['role_id' => $mrRole->id, 'name' => 'Dr. Kareem Tarek']);

        $cycle = VisitCycle::create([
            'name' => 'September 2026 Cycle',
            'code' => 'CYCLE-2026-09',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'status' => 'active',
        ]);

        $classA = ContactClassification::where('code', 'A')->first();
        $specialty = ContactSpecialty::first();

        $contact = Contact::create([
            'code' => 'DOC-101',
            'name' => 'Dr. Ahmed El-Sherif',
            'classification_id' => $classA->id,
            'specialty_id' => $specialty->id,
            'status' => 'active',
            'region' => 'Nasr City',
            'address' => '10 Tayaran St, Clinic 4B',
        ]);

        ContactAssignment::create([
            'cycle_id' => $cycle->id,
            'mr_id' => $mr->id,
            'contact_id' => $contact->id,
            'target_visits' => 3,
            'visits_done' => 1,
            'is_active' => true,
        ]);

        // Request the Excel export (.xlsx)
        $response = $this->actingAs($admin)
            ->get(route('admin.mr.reports.coverage', [
                'cycle_id' => $cycle->id,
                'export' => 'xlsx',
            ]));

        $response->assertStatus(200);
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type')
        );

        // Capture streamed content
        $streamedContent = $response->streamedContent();
        $this->assertNotEmpty($streamedContent);

        // Load with PhpSpreadsheet to verify professional formatting and AutoFilter
        $tempFile = tempnam(sys_get_temp_dir(), 'test_cov_') . '.xlsx';
        file_put_contents($tempFile, $streamedContent);

        $spreadsheet = IOFactory::load($tempFile);
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Verify Brand Header Banner (A1)
        $this->assertStringContainsString('DOCTOR COVERAGE', $sheet->getCell('A1')->getValue());

        // 2. Verify Table Headers (Row 6)
        $this->assertEquals('Contact Code', $sheet->getCell('A6')->getValue());
        $this->assertEquals('Doctor / Contact Name', $sheet->getCell('B6')->getValue());
        $this->assertEquals('Specialty', $sheet->getCell('C6')->getValue());
        $this->assertEquals('Class', $sheet->getCell('D6')->getValue());
        $this->assertEquals('Coverage Status', $sheet->getCell('N6')->getValue());

        // 3. Verify AutoFilter is enabled
        $autoFilterRange = $sheet->getAutoFilter()->getRange();
        $this->assertNotEmpty($autoFilterRange, 'AutoFilter should be set on the spreadsheet');
        $this->assertStringStartsWith('A6:N', $autoFilterRange);

        // 4. Verify Doctor Record (Row 7)
        $this->assertEquals('DOC-101', $sheet->getCell('A7')->getValue());
        $this->assertEquals('Dr. Ahmed El-Sherif', $sheet->getCell('B7')->getValue());
        $this->assertEquals('A', $sheet->getCell('D7')->getValue());
        $this->assertEquals(1, $sheet->getCell('I7')->getValue()); // visits done
        $this->assertEquals('Behind Frequency', $sheet->getCell('N7')->getValue());

        // 5. Verify Freeze Panes
        $this->assertEquals('A7', $sheet->getFreezePane());

        // Cleanup
        @unlink($tempFile);
    }
}
