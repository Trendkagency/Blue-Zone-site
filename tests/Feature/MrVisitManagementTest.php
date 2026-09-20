<?php

namespace Tests\Feature;

use App\Jobs\EvaluateDailyRepLogsJob;
use App\Models\Mr\Contact;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\ContactClassification;
use App\Models\Mr\ContactSpecialty;
use App\Models\Mr\RepDailyLog;
use App\Models\Mr\RepPerformanceSnapshot;
use App\Models\Mr\ScheduledVisit;
use App\Models\Mr\Visit;
use App\Models\Mr\VisitCycle;
use App\Models\Role;
use App\Models\User;
use App\Services\Mr\CrmAssignmentService;
use App\Services\Mr\CrmMrReportService;
use App\Services\Mr\CrmScheduleService;
use App\Services\Mr\CrmVisitService;
use App\Services\Mr\GpsValidationService;
use App\Services\Mr\Strategies\ClassificationPointStrategy;
use Database\Seeders\ContactClassificationSeeder;
use Database\Seeders\ContactSpecialtySeeder;
use Database\Seeders\MrGpsConfigSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MrVisitManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            ContactClassificationSeeder::class,
            ContactSpecialtySeeder::class,
            MrGpsConfigSeeder::class,
        ]);
    }

    /**
     * 1. Test Classifications & Defaults
     */
    public function test_contact_classifications_and_specialties_defaults(): void
    {
        $classA_plus = ContactClassification::where('code', 'A+')->first();
        $classA = ContactClassification::where('code', 'A')->first();
        $classB = ContactClassification::where('code', 'B')->first();
        $classC = ContactClassification::where('code', 'C')->first();

        $this->assertNotNull($classA_plus);
        $this->assertEquals(4, $classA_plus->points);
        $this->assertEquals(4, $classA_plus->required_visits);

        $this->assertNotNull($classA);
        $this->assertEquals(3, $classA->points);
        $this->assertEquals(3, $classA->required_visits);

        $this->assertNotNull($classB);
        $this->assertEquals(2, $classB->points);
        $this->assertEquals(2, $classB->required_visits);

        $this->assertNotNull($classC);
        $this->assertEquals(1, $classC->points);
        $this->assertEquals(1, $classC->required_visits);
    }

    /**
     * 2. Test GPS Validation Service & Geofencing
     */
    public function test_gps_validation_service_haversine_and_geofencing(): void
    {
        $gpsService = GpsValidationService::getInstance();

        // Cairo coordinates: 30.0444, 31.2357
        $clinicLat = 30.044420;
        $clinicLng = 31.235712;

        // Point A: ~50 meters away
        $closeLat = 30.044700;
        $closeLng = 31.235900;
        $resClose = $gpsService->validateCheckIn($closeLat, $closeLng, $clinicLat, $clinicLng);
        $this->assertTrue($resClose['verified']);
        $this->assertEquals('verified', $resClose['flag']);
        $this->assertLessThan(150, $resClose['distance_m']);

        // Point B: ~5 km away (Distance Exceeded)
        $farLat = 30.080000;
        $farLng = 31.280000;
        $resFar = $gpsService->validateCheckIn($farLat, $farLng, $clinicLat, $clinicLng);
        $this->assertFalse($resFar['verified']);
        $this->assertEquals('distance_exceeded', $resFar['flag']);
        $this->assertGreaterThan(150, $resFar['distance_m']);

        // Point C: GPS Disabled / Missing Coords
        $resDisabled = $gpsService->validateCheckIn(null, null, $clinicLat, $clinicLng);
        $this->assertFalse($resDisabled['verified']);
        $this->assertEquals('gps_disabled', $resDisabled['flag']);

        // Point D: Mock Location Detection
        $resMock = $gpsService->validateCheckIn($closeLat, $closeLng, $clinicLat, $clinicLng, null, ['is_mock' => true]);
        $this->assertFalse($resMock['verified']);
        $this->assertEquals('mock_suspected', $resMock['flag']);
    }

    /**
     * 3. Test Strict Per-Contact Points Capping & Strategy
     */
    public function test_strict_per_contact_points_capping(): void
    {
        $strategy = new ClassificationPointStrategy();

        $classA = ContactClassification::where('code', 'A')->first(); // 3 pts / visit, 3 required visits

        $specialty = ContactSpecialty::first();
        $contact = Contact::create([
            'code' => 'DOC-TEST-01',
            'name' => 'Dr. Test Prescriber',
            'classification_id' => $classA->id,
            'specialty_id' => $specialty->id,
        ]);

        $cycle = VisitCycle::create([
            'name' => 'September 2026 Test',
            'code' => 'CYCLE-TEST-01',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $assignment = ContactAssignment::create([
            'cycle_id' => $cycle->id,
            'mr_id' => $user->id,
            'contact_id' => $contact->id,
            'target_visits' => 3,
            'target_points' => 9,
        ]);

        // Target Points = 3 * 3 = 9
        $this->assertEquals(9, $strategy->calculateTargetPoints($contact, $classA));

        // 1 Visit Done -> 1 * 3 = 3 points
        $this->assertEquals(3, $strategy->calculateAchievedPoints($assignment, 1));

        // 2 Visits Done -> 2 * 3 = 6 points
        $this->assertEquals(6, $strategy->calculateAchievedPoints($assignment, 2));

        // 3 Visits Done -> 3 * 3 = 9 points
        $this->assertEquals(9, $strategy->calculateAchievedPoints($assignment, 3));

        // 6 Visits Done (Over-visiting attempt) -> STRICTLY CAPPED AT 9 points!
        $this->assertEquals(9, $strategy->calculateAchievedPoints($assignment, 6));
        $this->assertNotEquals(18, $strategy->calculateAchievedPoints($assignment, 6));
    }

    /**
     * 4. Test Check-In / Check-Out Lifecycle & Observer Trigger
     */
    public function test_visit_checkin_checkout_lifecycle_and_observer(): void
    {
        $classB = ContactClassification::where('code', 'B')->first(); // 2 pts, 2 visits
        $specialty = ContactSpecialty::first();

        $contact = Contact::create([
            'code' => 'DOC-TEST-02',
            'name' => 'Dr. Clinic Test',
            'classification_id' => $classB->id,
            'specialty_id' => $specialty->id,
            'latitude' => 30.044420,
            'longitude' => 31.235712,
        ]);

        $cycle = VisitCycle::create([
            'name' => 'Test Cycle 2026',
            'code' => 'CYCLE-TEST-02',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $assignmentService = app(CrmAssignmentService::class);
        $assignment = $assignmentService->assignContact($cycle->id, $user->id, $contact->id);

        $visitService = app(CrmVisitService::class);

        // Submit Check-In within clinic coordinates
        $visit = $visitService->submitCheckIn($user->id, [
            'contact_id' => $contact->id,
            'assignment_id' => $assignment->id,
            'cycle_id' => $cycle->id,
            'lat' => 30.044500,
            'lng' => 31.235800,
            'accuracy_m' => 10,
        ]);

        $this->assertNotNull($visit->id);
        $this->assertTrue($visit->gps_verified);
        $this->assertEquals('verified', $visit->gps_flag);

        // Verify RepDailyLog was automatically created and marked as reported by VisitObserver
        $todayLog = RepDailyLog::where('mr_id', $user->id)->whereDate('log_date', now()->toDateString())->first();
        $this->assertNotNull($todayLog);
        $this->assertTrue($todayLog->is_reported);
        $this->assertEquals(1, $todayLog->total_visits_count);

        // Submit Check-Out
        $completedVisit = $visitService->submitCheckOut($visit->id, $user->id, [
            'lat' => 30.044500,
            'lng' => 31.235800,
            'outcome' => 'completed',
            'notes' => 'Product presentation completed successfully.',
        ]);

        $this->assertNotNull($completedVisit->checkout_at);
        $this->assertEquals('completed', $completedVisit->outcome);

        // Verify ContactAssignment was automatically updated by VisitObserver
        $assignment->refresh();
        $this->assertEquals(1, $assignment->visits_done);
        $this->assertEquals(2, $assignment->achieved_points); // 1 visit * 2 pts
    }

    /**
     * 5. Test Performance Snapshot & KPI Calculations
     */
    public function test_kpi_snapshot_and_report_calculations(): void
    {
        $classA = ContactClassification::where('code', 'A')->first(); // 3 visits, 3 pts (Target: 9 pts)
        $classC = ContactClassification::where('code', 'C')->first(); // 1 visit, 1 pt (Target: 1 pt)
        $specialty = ContactSpecialty::first();

        $cycle = VisitCycle::create([
            'name' => 'Full KPI Cycle',
            'code' => 'CYCLE-KPI-01',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $doc1 = Contact::create([
            'code' => 'DOC-A',
            'name' => 'Doctor Alpha',
            'classification_id' => $classA->id,
            'specialty_id' => $specialty->id,
            'latitude' => 30.0444,
            'longitude' => 31.2357,
        ]);

        $doc2 = Contact::create([
            'code' => 'DOC-C',
            'name' => 'Doctor Charlie',
            'classification_id' => $classC->id,
            'specialty_id' => $specialty->id,
            'latitude' => 30.0500,
            'longitude' => 31.2400,
        ]);

        $assignService = app(CrmAssignmentService::class);
        $assignService->assignContact($cycle->id, $user->id, $doc1->id);
        $assignService->assignContact($cycle->id, $user->id, $doc2->id);

        $visitService = app(CrmVisitService::class);

        // Rep completes 2 visits for Doctor Alpha (Required: 3)
        $v1 = $visitService->submitCheckIn($user->id, ['contact_id' => $doc1->id, 'cycle_id' => $cycle->id, 'lat' => 30.0444, 'lng' => 31.2357]);
        $visitService->submitCheckOut($v1->id, $user->id, ['outcome' => 'completed']);

        $v2 = $visitService->submitCheckIn($user->id, ['contact_id' => $doc1->id, 'cycle_id' => $cycle->id, 'lat' => 30.0444, 'lng' => 31.2357]);
        $visitService->submitCheckOut($v2->id, $user->id, ['outcome' => 'completed']);

        // Calculate Report & Snapshots
        $reportService = app(CrmMrReportService::class);
        $snapshot = $reportService->recalculateRepSnapshot($user->id, $cycle->id);

        // 2 assigned doctors total, 1 doctor visited (Doctor Alpha)
        $this->assertEquals(2, $snapshot->total_assigned_contacts);
        $this->assertEquals(1, $snapshot->unique_contacts_visited);
        $this->assertEquals(50.0, (float) $snapshot->coverage_rate_pct); // 1 / 2 * 100 = 50%

        // Planned visits = 3 (Doc Alpha) + 1 (Doc Charlie) = 4 visits
        $this->assertEquals(4, $snapshot->planned_visits);
        $this->assertEquals(2, $snapshot->visits_done);
        $this->assertEquals(50.0, (float) $snapshot->visit_compliance_pct); // 2 / 4 * 100 = 50%

        // Target points = (3*3) + (1*1) = 10 pts
        $this->assertEquals(10, $snapshot->target_points);
        // Achieved points = min(2, 3)*3 + min(0, 1)*1 = 6 pts
        $this->assertEquals(6, $snapshot->achieved_points);
        $this->assertEquals(60.0, (float) $snapshot->points_achieved_pct); // 6 / 10 * 100 = 60%
    }

    /**
     * 6. Test Proactive At-Risk Doctors Detection
     */
    public function test_schedule_service_at_risk_detection(): void
    {
        $classA = ContactClassification::where('code', 'A+')->first(); // 4 visits required
        $specialty = ContactSpecialty::first();

        // Cycle ending in 5 days
        $cycle = VisitCycle::create([
            'name' => 'Near Ending Cycle',
            'code' => 'CYCLE-ENDING-01',
            'start_date' => now()->subDays(25),
            'end_date' => now()->addDays(4),
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $doc = Contact::create([
            'code' => 'DOC-RISK-01',
            'name' => 'Dr. High Priority Risk',
            'classification_id' => $classA->id,
            'specialty_id' => $specialty->id,
        ]);

        $assignService = app(CrmAssignmentService::class);
        $assignment = $assignService->assignContact($cycle->id, $user->id, $doc->id);

        $scheduleService = app(CrmScheduleService::class);
        $atRisk = $scheduleService->getAtRiskAssignmentsForRep($user->id, $cycle->id);

        $this->assertCount(1, $atRisk);
        $this->assertEquals($doc->id, $atRisk->first()->contact_id);
    }

    /**
     * 7. Test Reports Exact Column Structure
     */
    public function test_reports_data_generation_columns(): void
    {
        $classA = ContactClassification::where('code', 'A')->first();
        $specialty = ContactSpecialty::first();

        $cycle = VisitCycle::create([
            'name' => 'Reports Test Cycle',
            'code' => 'CYCLE-REP-01',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $doc = Contact::create([
            'code' => 'DOC-REP-01',
            'name' => 'Dr. Report Sample',
            'classification_id' => $classA->id,
            'specialty_id' => $specialty->id,
            'address' => '10 Tahrir Square, Cairo',
            'region' => 'Downtown',
        ]);

        $assignService = app(CrmAssignmentService::class);
        $assignService->assignContact($cycle->id, $user->id, $doc->id);

        $reportService = app(CrmMrReportService::class);
        $unvisitedReport = $reportService->getUnvisitedCoverageReport($cycle->id);

        $this->assertNotEmpty($unvisitedReport);
        $firstRow = $unvisitedReport->first();

        $this->assertArrayHasKey('contact_code', $firstRow);
        $this->assertArrayHasKey('contact_name', $firstRow);
        $this->assertArrayHasKey('region_city', $firstRow);
        $this->assertArrayHasKey('specialty', $firstRow);
        $this->assertArrayHasKey('address', $firstRow);
        $this->assertArrayHasKey('class', $firstRow);
        $this->assertArrayHasKey('required_frequency', $firstRow);
        $this->assertArrayHasKey('assigned_user', $firstRow);
        $this->assertArrayHasKey('visits_done', $firstRow);
        $this->assertArrayHasKey('target_points', $firstRow);
        $this->assertArrayHasKey('achieved_points', $firstRow);
        $this->assertArrayHasKey('visit_compliance_pct', $firstRow);
    }

    /**
     * 8. Test MR Dashboard Controller Check-In and Check-Out Endpoints
     */
    public function test_mr_mobile_portal_checkin_checkout_api_endpoints(): void
    {
        $classB = ContactClassification::where('code', 'B')->first();
        $specialty = ContactSpecialty::first();

        $cycle = VisitCycle::create([
            'name' => 'Endpoint Test Cycle',
            'code' => 'CYCLE-ENDP-01',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $doc = Contact::create([
            'code' => 'DOC-ENDP-01',
            'name' => 'Dr. Endpoint Test',
            'classification_id' => $classB->id,
            'specialty_id' => $specialty->id,
            'latitude' => 30.044420,
            'longitude' => 31.235712,
        ]);

        $assignService = app(CrmAssignmentService::class);
        $assignService->assignContact($cycle->id, $user->id, $doc->id);

        // Act as MR user and hit checkin endpoint
        $response = $this->actingAs($user)->postJson(route('mr.checkin'), [
            'contact_id' => $doc->id,
            'cycle_id' => $cycle->id,
            'lat' => 30.044450,
            'lng' => 31.235720,
            'accuracy_m' => 8,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'gps_verified' => true,
        ]);

        $visitId = $response->json('visit.id');
        $this->assertNotNull($visitId);

        // Hit checkout endpoint
        $checkoutResponse = $this->actingAs($user)->postJson(route('mr.checkout'), [
            'visit_id' => $visitId,
            'outcome' => 'completed',
            'notes' => 'Discussed longevity protocols.',
        ]);

        $checkoutResponse->assertStatus(200);
        $checkoutResponse->assertJson([
            'success' => true,
        ]);
    }
}
