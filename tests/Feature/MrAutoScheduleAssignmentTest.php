<?php

namespace Tests\Feature;

use App\Models\Mr\Contact;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\ContactClassification;
use App\Models\Mr\ContactSpecialty;
use App\Models\Mr\ScheduledVisit;
use App\Models\Mr\VisitCycle;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\ContactClassificationSeeder;
use Database\Seeders\ContactSpecialtySeeder;
use Database\Seeders\MrGpsConfigSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MrAutoScheduleAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Role $adminRole;
    protected User $medicalRep;
    protected VisitCycle $cycle;
    protected ContactClassification $classA;
    protected ContactSpecialty $specialty;

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
            'name' => 'Medical Representative',
            'description' => 'MR field staff',
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
    }

    /**
     * Helper to create doctor contacts
     */
    protected function createDoctor(string $code, string $name): Contact
    {
        return Contact::create([
            'code' => $code,
            'name' => $name,
            'hospital_clinic_name' => 'Longevity Medical Center',
            'classification_id' => $this->classA->id,
            'specialty_id' => $this->specialty->id,
            'latitude' => 30.0444,
            'longitude' => 31.2357,
            'status' => 'active',
        ]);
    }

    /**
     * Test assigning doctors with weekly auto-scheduling creates visits starting Today
     * and shows up in MR Portal today's agenda.
     */
    public function test_assign_doctors_with_weekly_auto_schedule_appears_in_mr_portal_today(): void
    {
        $doc1 = $this->createDoctor('DOC-SCHED-01', 'Dr. Ahmed El-Sherif');
        $doc2 = $this->createDoctor('DOC-SCHED-02', 'Dr. Sarah Mansour');

        $response = $this->actingAs($this->admin)->post(route('admin.mr.assignments.store'), [
            'cycle_id' => $this->cycle->id,
            'mr_id' => $this->medicalRep->id,
            'contact_ids' => [$doc1->id, $doc2->id],
            'auto_schedule' => '1',
            'schedule_cadence' => 'weekly',
            'schedule_today' => '1',
            'schedule_start_date' => now()->toDateString(),
            'schedule_time' => '09:30',
        ]);

        $response->assertRedirect(route('admin.mr.assignments.index', ['cycle_id' => $this->cycle->id]));
        $response->assertSessionHas('success');

        // Verify ContactAssignment records exist
        $this->assertDatabaseHas('mr_contact_assignments', [
            'cycle_id' => $this->cycle->id,
            'mr_id' => $this->medicalRep->id,
            'contact_id' => $doc1->id,
        ]);
        $this->assertDatabaseHas('mr_contact_assignments', [
            'cycle_id' => $this->cycle->id,
            'mr_id' => $this->medicalRep->id,
            'contact_id' => $doc2->id,
        ]);

        // Verify ScheduledVisit records created
        $scheduledVisits = ScheduledVisit::where('mr_id', $this->medicalRep->id)->get();
        $this->assertNotEmpty($scheduledVisits);

        // Verify today's visits were scheduled
        $todayVisits = ScheduledVisit::where('mr_id', $this->medicalRep->id)
            ->whereDate('scheduled_at', now()->toDateString())
            ->get();
        $this->assertCount(2, $todayVisits);

        // Verify times were staggered (e.g. 09:30 and 10:30)
        $this->assertNotEquals($todayVisits[0]->scheduled_at->toTimeString(), $todayVisits[1]->scheduled_at->toTimeString());

        // Now act as MR and visit MR portal (/mr)
        $portalResponse = $this->actingAs($this->medicalRep)->get(route('mr.dashboard'));
        $portalResponse->assertStatus(200);
        $portalResponse->assertSee('Dr. Ahmed El-Sherif');
        $portalResponse->assertSee('Dr. Sarah Mansour');
        $portalResponse->assertViewHas('todayVisits', function ($visits) {
            return count($visits) === 2;
        });
    }

    /**
     * Test daily cadence creates visits on consecutive days
     */
    public function test_assign_doctors_with_daily_cadence(): void
    {
        $doc = $this->createDoctor('DOC-DAILY-01', 'Dr. Daily Test');

        $response = $this->actingAs($this->admin)->post(route('admin.mr.assignments.store'), [
            'cycle_id' => $this->cycle->id,
            'mr_id' => $this->medicalRep->id,
            'contact_ids' => [$doc->id],
            'auto_schedule' => '1',
            'schedule_cadence' => 'daily',
            'schedule_today' => '1',
            'schedule_start_date' => now()->toDateString(),
            'schedule_time' => '10:00',
        ]);

        $response->assertRedirect();
        $scheduledVisits = ScheduledVisit::where('mr_id', $this->medicalRep->id)
            ->where('contact_id', $doc->id)
            ->orderBy('scheduled_at')
            ->get();

        // Class A requires 3 visits
        $this->assertCount(3, $scheduledVisits);
        $this->assertEquals(now()->toDateString(), $scheduledVisits[0]->scheduled_at->toDateString());
    }

    /**
     * Test monthly cadence distributes visits across cycle
     */
    public function test_assign_doctors_with_monthly_cadence(): void
    {
        $doc = $this->createDoctor('DOC-MONTHLY-01', 'Dr. Monthly Test');

        $response = $this->actingAs($this->admin)->post(route('admin.mr.assignments.store'), [
            'cycle_id' => $this->cycle->id,
            'mr_id' => $this->medicalRep->id,
            'contact_ids' => [$doc->id],
            'auto_schedule' => '1',
            'schedule_cadence' => 'monthly',
            'schedule_today' => '1',
            'schedule_start_date' => now()->toDateString(),
            'schedule_time' => '11:00',
        ]);

        $response->assertRedirect();
        $scheduledVisits = ScheduledVisit::where('mr_id', $this->medicalRep->id)
            ->where('contact_id', $doc->id)
            ->orderBy('scheduled_at')
            ->get();

        $this->assertCount(3, $scheduledVisits);
        $this->assertEquals(now()->toDateString(), $scheduledVisits[0]->scheduled_at->toDateString());
    }

    /**
     * Test assigning without auto_schedule keeps backward compatibility
     */
    public function test_assign_without_auto_schedule_does_not_create_visits(): void
    {
        $doc = $this->createDoctor('DOC-NO-SCHED-01', 'Dr. No Sched');

        $response = $this->actingAs($this->admin)->post(route('admin.mr.assignments.store'), [
            'cycle_id' => $this->cycle->id,
            'mr_id' => $this->medicalRep->id,
            'contact_ids' => [$doc->id],
            // auto_schedule omitted / null
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('mr_contact_assignments', [
            'cycle_id' => $this->cycle->id,
            'mr_id' => $this->medicalRep->id,
            'contact_id' => $doc->id,
        ]);

        $scheduledVisits = ScheduledVisit::where('mr_id', $this->medicalRep->id)
            ->where('contact_id', $doc->id)
            ->get();
        $this->assertCount(0, $scheduledVisits);
    }
}
