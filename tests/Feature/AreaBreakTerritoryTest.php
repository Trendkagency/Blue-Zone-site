<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\AreaBreak;
use App\Models\City;
use App\Models\Country;
use App\Models\Role;
use App\Models\User;
use App\Services\Mr\AreaTerritoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AreaBreakTerritoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Country $country;
    protected City $city;
    protected Area $area;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'super_admin'], ['display_name' => 'Super Admin']);
        $this->admin = User::factory()->create([
            'role_id' => $role->id,
            'email' => 'admin_test_' . uniqid() . '@example.com',
        ]);

        $this->country = Country::firstOrCreate(['iso2' => 'EG'], [
            'iso3' => 'EGY',
            'name_en' => 'Egypt',
            'name_ar' => 'مصر',
            'phone_code' => '+20',
            'is_active' => true,
        ]);

        $this->city = City::firstOrCreate(['country_id' => $this->country->id, 'name_en' => 'Cairo'], [
            'name_ar' => 'القاهرة',
            'is_active' => true,
        ]);

        $this->area = Area::firstOrCreate(['city_id' => $this->city->id, 'name_en' => 'Central Cairo Area'], [
            'country_id' => $this->country->id,
            'name_ar' => 'وسط القاهرة',
            'code' => 'CAI-CEN',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_breaks_index()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.mr.breaks.index'));
        $response->assertStatus(200);
        $response->assertSee('Territory Breaks');
    }

    public function test_admin_can_create_a_break()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.mr.breaks.store'), [
            'country_id' => $this->country->id,
            'city_id' => $this->city->id,
            'area_id' => $this->area->id,
            'name_en' => 'Zamalek Sector 1',
            'name_ar' => 'قطاع الزمالك 1',
            'code' => 'CAI-ZAM-01',
            'sort_order' => 1,
            'is_active' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('breaks', [
            'name_en' => 'Zamalek Sector 1',
            'code' => 'CAI-ZAM-01',
            'area_id' => $this->area->id,
        ]);
    }

    public function test_admin_can_update_a_break()
    {
        $break = AreaBreak::create([
            'country_id' => $this->country->id,
            'city_id' => $this->city->id,
            'area_id' => $this->area->id,
            'name_en' => 'Old Break Name',
            'name_ar' => 'اسم بريك قديم',
            'code' => 'BRK-OLD',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.mr.breaks.update', $break->id), [
            'country_id' => $this->country->id,
            'city_id' => $this->city->id,
            'area_id' => $this->area->id,
            'name_en' => 'Updated Break Name',
            'name_ar' => 'اسم بريك محدث',
            'code' => 'BRK-NEW',
            'sort_order' => 5,
            'is_active' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('breaks', [
            'id' => $break->id,
            'name_en' => 'Updated Break Name',
            'code' => 'BRK-NEW',
        ]);
    }

    public function test_admin_can_toggle_break_status()
    {
        $break = AreaBreak::create([
            'country_id' => $this->country->id,
            'city_id' => $this->city->id,
            'area_id' => $this->area->id,
            'name_en' => 'Toggleable Break',
            'name_ar' => 'بريك للتفعيل',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.mr.breaks.toggle-status', $break->id));
        $response->assertRedirect();

        $this->assertFalse($break->fresh()->is_active);
    }

    public function test_ajax_get_breaks_by_area()
    {
        $break = AreaBreak::create([
            'country_id' => $this->country->id,
            'city_id' => $this->city->id,
            'area_id' => $this->area->id,
            'name_en' => 'Ajax Sub-Sector',
            'name_ar' => 'قطاع فرعي للأياكس',
            'code' => 'AJX-BRK',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('admin.api.areas.breaks', $this->area->id));
        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonFragment(['name_en' => 'Ajax Sub-Sector']);
    }

    public function test_singleton_area_territory_service_handles_breaks()
    {
        $service = AreaTerritoryService::getInstance();
        $this->assertInstanceOf(AreaTerritoryService::class, $service);

        $break = AreaBreak::create([
            'country_id' => $this->country->id,
            'city_id' => $this->city->id,
            'area_id' => $this->area->id,
            'name_en' => 'Singleton Test Break',
            'name_ar' => 'بريك اختبار السينجلتون',
            'is_active' => true,
        ]);

        $breaks = $service->getBreaksByArea($this->area->id);
        $this->assertTrue($breaks->contains('id', $break->id));

        $user = User::factory()->create();
        $updatedUser = $service->assignRepToTerritory($user, $break->id);

        $this->assertEquals($break->id, $updatedUser->break_id);
        $this->assertEquals($this->area->id, $updatedUser->area_id);
        $this->assertEquals($this->city->id, $updatedUser->city_id);
        $this->assertEquals($this->country->id, $updatedUser->country_id);
    }
}
