<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\Mr\Contact;
use App\Models\Mr\ContactClassification;
use App\Models\Mr\ContactSpecialty;
use App\Models\Mr\VisitCycle;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminMrNativeRoutesTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'super_admin'], ['permissions' => json_encode(['*'])]);
        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test@bluezone.com'],
            [
                'name' => 'Admin Test',
                'password' => bcrypt('password'),
                'role_id' => $role->id,
                'status' => 'active',
            ]
        );
    }

    public function test_native_mr_pages_render_successfully(): void
    {
        $this->actingAs($this->admin);

        // 1. Live Ops Map
        $response = $this->get(route('admin.mr.live-map'));
        $response->assertStatus(200);

        // 2. Contacts (Doctors & Clinics)
        $response = $this->get(route('admin.mr.contacts.index'));
        $response->assertStatus(200);

        $response = $this->get(route('admin.mr.contacts.create'));
        $response->assertStatus(200);

        // 3. Classifications (A+/A/B/C)
        $response = $this->get(route('admin.mr.classifications.index'));
        $response->assertStatus(200);

        // 4. Specialties
        $response = $this->get(route('admin.mr.specialties.index'));
        $response->assertStatus(200);

        // 5. Assignments
        $response = $this->get(route('admin.mr.assignments.index'));
        $response->assertStatus(200);

        // 6. Cycles
        $response = $this->get(route('admin.mr.cycles.index'));
        $response->assertStatus(200);

        // 7. Visits Log
        $response = $this->get(route('admin.mr.visits.index'));
        $response->assertStatus(200);

        // 8. Coverage Report
        $response = $this->get(route('admin.mr.reports.coverage'));
        $response->assertStatus(200);

        // 9. Performance Scorecard
        $response = $this->get(route('admin.mr.reports.performance'));
        $response->assertStatus(200);

        // 10. GPS Config
        $response = $this->get(route('admin.mr.gps-config.index'));
        $response->assertStatus(200);
    }
}
