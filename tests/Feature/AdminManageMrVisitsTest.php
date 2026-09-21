<?php

namespace Tests\Feature;

use App\Models\Mr\Contact;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\ContactClassification;
use App\Models\Mr\ContactSpecialty;
use App\Models\Mr\ScheduledVisit;
use App\Models\Mr\Visit;
use App\Models\Mr\VisitCycle;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\ContactClassificationSeeder;
use Database\Seeders\ContactSpecialtySeeder;
use Database\Seeders\MrGpsConfigSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManageMrVisitsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Role $adminRole;
    protected User $medicalRep;
    protected VisitCycle $cycle;
    protected ContactClassification $classA;
    protected ContactSpecialty $specialty;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->seed([
            ContactClassificationSeeder::class,
            ContactSpecialtySeeder::class,
            MrGpsConfigSeeder::class,
        ]);

        $this->adminRole = Role::create([
            'name' => 'Super Admin',
            'description' => 'Administrator',
            'permissions' => ['*'],
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active',
        ]);

        $mrRole = Role::create([
            'name' => 'mr',
            'description' => 'MR staff',
            'permissions' => ['mr.access'],
        ]);

        $this->medicalRep = User::factory()->create([
            'role_id' => $mrRole->id,
            'status' => 'active',
            'name' => 'Dr. Kareem Tarek',
        ]);

        $this->cycle = VisitCycle::create([
            'name' => 'Q3 Cycle 2026',
            'code' => 'CYCLE-Q3-2026',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'status' => 'active',
        ]);

        $this->classA = ContactClassification::where('code', 'A')->firstOrFail();
        $this->specialty = ContactSpecialty::firstOrFail();

        $this->product = Product::create([
            'name_en' => 'Blue Cell Longevity Booster',
            'name_ar' => 'محفز طول العمر الخلوي',
            'slug' => 'blue-cell-longevity-booster',
            'sku' => 'BZ-CELL-001',
            'price' => 1250.00,
            'stock' => 100,
            'is_active' => true,
        ]);
    }

    protected function createDoctor(string $code, string $name): Contact
    {
        return Contact::create([
            'code' => $code,
            'name' => $name,
            'hospital_clinic_name' => 'Cairo Heart & Longevity Center',
            'classification_id' => $this->classA->id,
            'specialty_id' => $this->specialty->id,
            'latitude' => 30.0444,
            'longitude' => 31.2357,
            'status' => 'active',
        ]);
    }

    /**
     * 1. Test Admin can schedule a visit for any MR for Today
     * and it immediately appears in the MR portal agenda.
     */
    public function test_admin_can_schedule_visit_for_mr_for_today(): void
    {
        $doc = $this->createDoctor('DOC-ADM-01', 'Dr. Ahmed El-Sherif');

        $response = $this->actingAs($this->admin)->post(route('admin.mr.visits.schedule'), [
            'mr_id' => $this->medicalRep->id,
            'contact_id' => $doc->id,
            'scheduled_date' => now()->toDateString(),
            'scheduled_time' => '10:30',
            'notes' => 'Admin scheduled visit: present clinical longevity trials',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check ScheduledVisit created
        $scheduledVisit = ScheduledVisit::where('mr_id', $this->medicalRep->id)
            ->where('contact_id', $doc->id)
            ->whereDate('scheduled_at', now()->toDateString())
            ->first();

        $this->assertNotNull($scheduledVisit);
        $this->assertEquals('planned', $scheduledVisit->status);

        // Check assignment was ensured
        $this->assertDatabaseHas('mr_contact_assignments', [
            'cycle_id' => $this->cycle->id,
            'mr_id' => $this->medicalRep->id,
            'contact_id' => $doc->id,
        ]);

        // Verify it appears in MR Portal Today's Field Agenda
        $mrPortalResponse = $this->actingAs($this->medicalRep)->get(route('mr.dashboard'));
        $mrPortalResponse->assertStatus(200);
        $mrPortalResponse->assertSee('Dr. Ahmed El-Sherif');
    }

    /**
     * 2. Test Admin can record a direct completed visit on behalf of an MR
     */
    public function test_admin_can_record_direct_completed_visit_for_mr(): void
    {
        $doc = $this->createDoctor('DOC-ADM-02', 'Dr. Laila Hassan');

        // Pre-create planned visit
        $scheduledVisit = ScheduledVisit::create([
            'mr_id' => $this->medicalRep->id,
            'contact_id' => $doc->id,
            'cycle_id' => $this->cycle->id,
            'scheduled_at' => now(),
            'status' => 'planned',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.mr.visits.record-direct'), [
            'mr_id' => $this->medicalRep->id,
            'contact_id' => $doc->id,
            'scheduled_visit_id' => $scheduledVisit->id,
            'visited_at' => now()->toDateTimeString(),
            'outcome' => 'successful',
            'notes' => 'Meeting completed successfully with Dr. Laila. Product samples provided.',
            'product_ids' => [$this->product->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify Visit was created
        $visit = Visit::where('mr_id', $this->medicalRep->id)
            ->where('contact_id', $doc->id)
            ->first();

        $this->assertNotNull($visit);
        $this->assertEquals('successful', $visit->outcome);
        $this->assertTrue((bool)$visit->gps_verified);
        $this->assertEquals($this->product->id, $visit->product_id);
        $this->assertCount(1, $visit->products);

        // Verify ScheduledVisit status updated
        $this->assertEquals('completed', $scheduledVisit->fresh()->status);

        // Verify assignment points and visits_done incremented
        $assignment = ContactAssignment::where('cycle_id', $this->cycle->id)
            ->where('mr_id', $this->medicalRep->id)
            ->where('contact_id', $doc->id)
            ->first();

        $this->assertNotNull($assignment);
        $this->assertEquals(1, $assignment->visits_done);
        $this->assertGreaterThan(0, $assignment->achieved_points);
    }

    /**
     * 3. Test Admin can cancel a scheduled visit
     */
    public function test_admin_can_cancel_scheduled_visit(): void
    {
        $doc = $this->createDoctor('DOC-ADM-03', 'Dr. Cancelled Visit');

        $scheduledVisit = ScheduledVisit::create([
            'mr_id' => $this->medicalRep->id,
            'contact_id' => $doc->id,
            'cycle_id' => $this->cycle->id,
            'scheduled_at' => now(),
            'status' => 'planned',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.mr.visits.cancel-schedule', $scheduledVisit->id));
        $response->assertRedirect();
        $this->assertEquals('cancelled', $scheduledVisit->fresh()->status);
    }

    /**
     * 4. Test Assign Doctors Every Day (Continuous daily including weekends)
     */
    public function test_assign_doctors_every_day_continuous_cadence(): void
    {
        $doc = $this->createDoctor('DOC-ADM-04', 'Dr. Daily Recurrence');

        $response = $this->actingAs($this->admin)->post(route('admin.mr.assignments.store'), [
            'cycle_id' => $this->cycle->id,
            'mr_id' => $this->medicalRep->id,
            'contact_ids' => [$doc->id],
            'auto_schedule' => '1',
            'schedule_cadence' => 'daily',
            'schedule_today' => '1',
            'daily_all_days' => '1', // Every day continuous
            'daily_count' => 4,
            'schedule_start_date' => now()->toDateString(),
            'schedule_time' => '10:00',
        ]);

        $response->assertRedirect();

        $scheduledVisits = ScheduledVisit::where('mr_id', $this->medicalRep->id)
            ->where('contact_id', $doc->id)
            ->orderBy('scheduled_at')
            ->get();

        $this->assertCount(4, $scheduledVisits);
        $this->assertEquals(now()->toDateString(), $scheduledVisits[0]->scheduled_at->toDateString());
    }
}
