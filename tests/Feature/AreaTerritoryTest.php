<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\Role;
use App\Models\User;
use App\Services\Mr\AreaTerritoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AreaTerritoryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Role $adminRole;
    private Role $mrRole;
    private Country $country;
    private City $city;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->adminRole = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['description' => 'Super Administrator with Full Access', 'permissions' => ['*']]
        );

        $this->mrRole = Role::firstOrCreate(
            ['name' => 'mr'],
            ['description' => 'Medical Representative', 'permissions' => ['mr.portal', 'visits.create']]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_territory_test@bluezone.com'],
            [
                'name' => 'Test Territory Admin',
                'password' => bcrypt('password123'),
                'role_id' => $this->adminRole->id,
                'status' => 'active',
            ]
        );

        $this->country = Country::firstOrCreate(
            ['iso2' => 'EG'],
            [
                'name_en' => 'Egypt',
                'name_ar' => 'مصر',
                'phone_code' => '+20',
                'currency_code' => 'EGP',
                'is_active' => true,
            ]
        );

        $this->city = City::firstOrCreate(
            ['country_id' => $this->country->id, 'name_en' => 'Cairo'],
            [
                'name_ar' => 'القاهرة',
                'shipping_cost' => 50.00,
                'is_active' => true,
            ]
        );
    }

    public function test_area_model_relations_and_localized_accessors(): void
    {
        $area = Area::create([
            'country_id' => $this->country->id,
            'city_id' => $this->city->id,
            'name_en' => 'New Cairo',
            'name_ar' => 'التجمع الخامس',
            'code' => 'CAI-NC-TEST',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->assertNotNull($area->id);
        $this->assertEquals($this->country->id, $area->country->id);
        $this->assertEquals($this->city->id, $area->city->id);

        app()->setLocale('en');
        $this->assertEquals('New Cairo', $area->name);
        $this->assertStringContainsString('New Cairo', $area->full_hierarchy_label);

        app()->setLocale('ar');
        $this->assertEquals('التجمع الخامس', $area->name);

        $this->assertTrue($this->city->areas()->where('areas.id', $area->id)->exists());
        $this->assertTrue($this->country->areas()->where('areas.id', $area->id)->exists());
    }

    public function test_singleton_area_territory_service(): void
    {
        $service1 = AreaTerritoryService::getInstance();
        $service2 = AreaTerritoryService::getInstance();
        $this->assertSame($service1, $service2);

        $rep = User::create([
            'name' => 'Field Rep Tarek',
            'email' => 'tarek_rep_' . uniqid() . '@bluezone.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->mrRole->id,
            'status' => 'active',
        ]);

        $area = Area::firstOrCreate(
            ['city_id' => $this->city->id, 'name_en' => 'Maadi'],
            [
                'country_id' => $this->country->id,
                'name_ar' => 'المعادي',
                'code' => 'CAI-MAA-TEST',
                'is_active' => true,
            ]
        );

        $updatedRep = $service1->assignRepToTerritory($rep, $area->id);

        $this->assertEquals($area->id, $updatedRep->area_id);
        $this->assertEquals($this->city->id, $updatedRep->city_id);
        $this->assertEquals($this->country->id, $updatedRep->country_id);
        $this->assertStringContainsString('Maadi', $updatedRep->territory_label);
    }

    public function test_dynamic_cascading_areas_api(): void
    {
        $area = Area::firstOrCreate(
            ['city_id' => $this->city->id, 'name_en' => 'Heliopolis'],
            [
                'country_id' => $this->country->id,
                'name_ar' => 'مصر الجديدة',
                'code' => 'CAI-HEL-TEST',
                'is_active' => true,
            ]
        );

        $response = $this->actingAs($this->admin, 'web')
            ->getJson(route('admin.api.cities.areas', ['id' => $this->city->id]));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'city' => ['id', 'name_en', 'name_ar', 'country_id'],
            'areas' => [
                '*' => ['id', 'city_id', 'country_id', 'name_en', 'name_ar', 'name', 'code']
            ]
        ]);
        $response->assertJsonFragment(['name_en' => 'Heliopolis']);
    }

    public function test_admin_can_perform_area_crud(): void
    {
        // 1. Index
        $resIndex = $this->actingAs($this->admin, 'web')->get(route('admin.mr.areas.index'));
        $resIndex->assertOk();

        // 2. Store
        $resStore = $this->actingAs($this->admin, 'web')->post(route('admin.mr.areas.store'), [
            'country_id' => $this->country->id,
            'city_id' => $this->city->id,
            'name_en' => 'Zamalek District',
            'name_ar' => 'حي الزمالك',
            'code' => 'CAI-ZAM-TEST',
            'sort_order' => 5,
            'is_active' => true,
        ]);
        $resStore->assertRedirect();
        $this->assertDatabaseHas('areas', [
            'name_en' => 'Zamalek District',
            'code' => 'CAI-ZAM-TEST',
        ]);

        $newArea = Area::where('code', 'CAI-ZAM-TEST')->first();

        // 3. Update
        $resUpdate = $this->actingAs($this->admin, 'web')->put(route('admin.mr.areas.update', $newArea->id), [
            'country_id' => $this->country->id,
            'city_id' => $this->city->id,
            'name_en' => 'Zamalek Island Updated',
            'name_ar' => 'جزيرة الزمالك محدثة',
            'code' => 'CAI-ZAM-UPDATED',
            'sort_order' => 10,
            'is_active' => true,
        ]);
        $resUpdate->assertRedirect();
        $this->assertDatabaseHas('areas', [
            'id' => $newArea->id,
            'name_en' => 'Zamalek Island Updated',
            'code' => 'CAI-ZAM-UPDATED',
        ]);

        // 4. Toggle Status
        $resToggle = $this->actingAs($this->admin, 'web')->post(route('admin.mr.areas.toggle-status', $newArea->id));
        $resToggle->assertRedirect();
        $this->assertFalse($newArea->fresh()->is_active);

        // 5. Delete
        $resDelete = $this->actingAs($this->admin, 'web')->delete(route('admin.mr.areas.destroy', $newArea->id));
        $resDelete->assertRedirect();
        $this->assertSoftDeleted('areas', ['id' => $newArea->id]);
    }

    public function test_user_creation_and_edit_with_territory_assignment(): void
    {
        $area = Area::firstOrCreate(
            ['city_id' => $this->city->id, 'name_en' => 'Dokki Area'],
            [
                'country_id' => $this->country->id,
                'name_ar' => 'منطقة الدقي',
                'code' => 'GIZ-DOK-TEST',
                'is_active' => true,
            ]
        );

        $email = 'new_rep_' . uniqid() . '@bluezone.com';

        // 1. Create User with Country, City, Area
        $resCreate = $this->actingAs($this->admin, 'web')->post(route('admin.users.store'), [
            'name' => 'Doctor Rep Mostafa',
            'email' => $email,
            'phone' => '+201012345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $this->mrRole->id,
            'country_id' => $this->country->id,
            'city_id' => $this->city->id,
            'area_id' => $area->id,
            'status' => 'active',
        ]);

        $resCreate->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'area_id' => $area->id,
            'city_id' => $this->city->id,
            'country_id' => $this->country->id,
        ]);

        $createdUser = User::where('email', $email)->first();
        $this->assertEquals($area->id, $createdUser->area_id);
        $this->assertStringContainsString('Dokki Area', $createdUser->territory_label);

        // 2. Edit User form loads territory data
        $resEdit = $this->actingAs($this->admin, 'web')->get(route('admin.users.edit', $createdUser->id));
        $resEdit->assertOk();
        $resEdit->assertSee('Dokki Area');
    }
}
