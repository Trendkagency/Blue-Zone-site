<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Location;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeoLocationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Country $saudi;
    private City $riyadh;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(
            ['name' => 'Super Administrator'],
            ['slug' => 'super-admin', 'permissions' => ['*'], 'is_system' => true]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@bluezone.com'],
            ['name' => 'Bluezone Admin', 'password' => bcrypt('password'), 'role_id' => $role->id, 'status' => 'active']
        );

        $this->saudi = Country::firstOrCreate(
            ['iso2' => 'SA'],
            [
                'name_en' => 'Saudi Arabia',
                'name_ar' => 'المملكة العربية السعودية',
                'phone_code' => '+966',
                'currency_code' => 'SAR',
                'currency_symbol_en' => 'SAR',
                'currency_symbol_ar' => 'ر.س',
                'flag_emoji' => '🇸🇦',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $this->riyadh = City::firstOrCreate(
            ['country_id' => $this->saudi->id, 'name_en' => 'Riyadh'],
            [
                'name_ar' => 'الرياض',
                'shipping_cost' => 25.00,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );
    }

    public function test_admin_can_view_geo_settings_index(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.settings.geo.index'));

        $response->assertStatus(200);
        $response->assertSee('Saudi Arabia');
        $response->assertSee('+966');
        $response->assertSee('Riyadh');
    }

    public function test_admin_can_create_new_country(): void
    {
        $payload = [
            'name_en' => 'Jordan',
            'name_ar' => 'المملكة الأردنية الهاشمية',
            'iso2' => 'jo',
            'phone_code' => '962',
            'currency_code' => 'JOD',
            'currency_symbol_en' => 'JOD',
            'currency_symbol_ar' => 'د.أ',
            'flag_emoji' => '🇯🇴',
            'sort_order' => 10,
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.countries.store'), $payload);

        $response->assertRedirect(route('admin.settings.geo.index'));
        $this->assertDatabaseHas('countries', [
            'iso2' => 'JO',
            'phone_code' => '+962',
            'name_en' => 'Jordan',
        ]);
    }

    public function test_admin_can_update_country(): void
    {
        $payload = [
            'name_en' => 'Kingdom of Saudi Arabia',
            'name_ar' => 'المملكة العربية السعودية الشقيقة',
            'iso2' => 'SA',
            'phone_code' => '+966',
            'currency_code' => 'SAR',
            'currency_symbol_en' => 'SAR',
            'currency_symbol_ar' => 'ر.س',
            'flag_emoji' => '🇸🇦',
            'sort_order' => 1,
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->admin, 'web')->put(route('admin.countries.update', $this->saudi->id), $payload);

        $response->assertRedirect(route('admin.settings.geo.index'));
        $this->assertDatabaseHas('countries', [
            'id' => $this->saudi->id,
            'name_en' => 'Kingdom of Saudi Arabia',
        ]);
    }

    public function test_admin_can_toggle_country_status(): void
    {
        $response = $this->actingAs($this->admin, 'web')->post(route('admin.countries.toggle-status', $this->saudi->id));

        $response->assertSessionHas('status');
        $this->assertFalse($this->saudi->fresh()->is_active);
    }

    public function test_admin_can_create_city_linked_to_country(): void
    {
        $payload = [
            'country_id' => $this->saudi->id,
            'name_en' => 'Khobar',
            'name_ar' => 'الخبر',
            'shipping_cost' => 30.00,
            'sort_order' => 2,
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.cities.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('cities', [
            'country_id' => $this->saudi->id,
            'name_en' => 'Khobar',
            'shipping_cost' => 30.00,
        ]);
    }

    public function test_admin_can_fetch_cities_by_country_via_ajax_api(): void
    {
        City::create([
            'country_id' => $this->saudi->id,
            'name_en' => 'Jeddah',
            'name_ar' => 'جدة',
            'shipping_cost' => 30.00,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin, 'web')->getJson(route('admin.api.countries.cities', $this->saudi->id));

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('country.iso2', 'SA');
        $response->assertJsonPath('country.phone_code', '+966');
        $response->assertJsonFragment(['name_en' => 'Riyadh']);
        $response->assertJsonFragment(['name_en' => 'Jeddah']);
    }

    public function test_admin_can_create_warehouse_with_dynamic_country_and_city(): void
    {
        $category = Category::create([
            'name_en' => 'Longevity Formulations',
            'name_ar' => 'تركيبات إطالة العمر',
            'slug' => 'longevity-formulations',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'sku' => 'SKU-NMN-100',
            'name_en' => 'Pure NMN 500mg',
            'name_ar' => 'إن إم إن نقي 500 ملغ',
            'slug' => 'pure-nmn-500mg',
            'price' => 150.00,
            'cost_price' => 60.00,
            'is_active' => true,
        ]);

        $payload = [
            'name_en' => 'Riyadh Central Logistics Depot',
            'name_ar' => 'مستودع الرياض المركزي اللوجستي',
            'id' => 'wh_riyadh_central',
            'code' => 'LOC-RUH-01',
            'type' => 'warehouse',
            'country_id' => $this->saudi->id,
            'city_id' => $this->riyadh->id,
            'address' => 'King Fahd Road, Sector 4',
            'manager_name' => 'Faisal Al-Otaibi',
            'phone' => '501234567', // un-prefixed, will auto-prefix with +966
            'capacity_units' => 20000,
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.warehouses.store'), $payload);

        $response->assertRedirect(route('admin.warehouses.index'));
        $this->assertDatabaseHas('locations', [
            'id' => 'wh_riyadh_central',
            'country_id' => $this->saudi->id,
            'city_id' => $this->riyadh->id,
            'city' => 'Riyadh',
            'phone' => '+966 501234567',
        ]);

        $loc = Location::find('wh_riyadh_central');
        $this->assertEquals('Saudi Arabia', $loc->country->name_en);
        $this->assertEquals('Riyadh', $loc->cityModel->name_en);
        $this->assertStringContainsString('+966 501234567', $loc->phone_with_code);
    }

    public function test_cannot_delete_country_when_locations_are_linked(): void
    {
        Location::create([
            'id' => 'loc_test_country_guard',
            'name_en' => 'Guard Test Hub',
            'name_ar' => 'مستودع تجريبي للحماية',
            'code' => 'LOC-GRD',
            'type' => 'warehouse',
            'country_id' => $this->saudi->id,
            'city_id' => $this->riyadh->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin, 'web')->delete(route('admin.countries.destroy', $this->saudi->id));

        $response->assertSessionHasErrors('delete_error');
        $this->assertDatabaseHas('countries', ['id' => $this->saudi->id]);
    }
}
