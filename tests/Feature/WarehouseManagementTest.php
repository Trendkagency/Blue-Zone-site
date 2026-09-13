<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Location;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Product $product;

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

        Location::firstOrCreate(
            ['id' => 'online'],
            [
                'name_en' => 'Online Fulfillment Hub',
                'name_ar' => 'مستودع الطلبات الإلكترونية',
                'code' => 'LOC-ONL',
                'type' => 'online',
                'is_active' => true,
            ]
        );

        Location::firstOrCreate(
            ['id' => 'offline'],
            [
                'name_en' => 'Warehouse / POS',
                'name_ar' => 'المتجر الرئيسي / المبيعات المباشرة',
                'code' => 'LOC-POS',
                'type' => 'offline',
                'is_active' => true,
            ]
        );

        Location::firstOrCreate(
            ['id' => 'central_wh'],
            [
                'name_en' => 'Central Quarantine Warehouse',
                'name_ar' => 'المستودع المركزي الرئيسي',
                'code' => 'LOC-CWH',
                'type' => 'warehouse',
                'is_active' => true,
            ]
        );

        $category = Category::create([
            'name_en' => 'Metabolic Science',
            'name_ar' => 'علوم الأيض',
            'slug' => 'metabolic-science',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'sku' => 'TEST-SKU-100',
            'name_en' => 'Longevity NMN Complex',
            'name_ar' => 'مركب إن إم إن المتقدم',
            'slug' => 'longevity-nmn-complex',
            'price' => 120.00,
            'cost_price' => 50.00,
            'stock_online' => 60,
            'stock_offline' => 30,
            'is_active' => true,
        ]);

        InventoryService::syncAllProductsInventory();
    }

    public function test_admin_can_view_warehouses_list(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.warehouses.index'));

        $response->assertStatus(200);
        $response->assertSee('Online Fulfillment Hub');
        $response->assertSee('Warehouse / POS');
        $response->assertSee('Central Quarantine Warehouse');
    }

    public function test_admin_can_access_locations_routes(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.locations.index'));

        $response->assertStatus(200);
        $response->assertSee('Online Fulfillment Hub');

        $showResponse = $this->actingAs($this->admin, 'web')->get(route('admin.locations.show', 'online'));
        $showResponse->assertStatus(200);
    }

    public function test_admin_can_create_new_warehouse_and_products_are_auto_provisioned(): void
    {
        $payload = [
            'name_en' => 'Jeddah Regional Logistics Center',
            'name_ar' => 'مركز جدة اللوجستي الإقليمي',
            'code' => 'LOC-JED-01',
            'id' => 'wh_jeddah',
            'type' => 'warehouse',
            'city' => 'Jeddah',
            'address' => 'Industrial Area 2, Depot 14',
            'manager_name' => 'Fahad Al-Harbi',
            'phone' => '+966501112233',
            'capacity_units' => 15000,
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.warehouses.store'), $payload);

        $response->assertRedirect(route('admin.warehouses.index'));
        $this->assertDatabaseHas('locations', [
            'id' => 'wh_jeddah',
            'code' => 'LOC-JED-01',
            'city' => 'Jeddah',
        ]);

        // Verify that InventoryItem for this new location was automatically provisioned
        $this->assertDatabaseHas('inventory_items', [
            'product_id' => $this->product->id,
            'location_id' => 'wh_jeddah',
            'location_name_en' => 'Jeddah Regional Logistics Center',
        ]);
    }

    public function test_admin_can_view_warehouse_details_and_inventory(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.warehouses.show', 'online'));

        $response->assertStatus(200);
        $response->assertSee('Online Fulfillment Hub');
        $response->assertSee($this->product->sku);
    }

    public function test_admin_can_update_warehouse(): void
    {
        $payload = [
            'name_en' => 'Updated Central Logistics Depot',
            'name_ar' => 'المستودع المركزي المطور',
            'code' => 'LOC-CWH-NEW',
            'type' => 'warehouse',
            'city' => 'Riyadh Logistics Park',
            'manager_name' => 'Dr. Khalid',
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->admin, 'web')->put(route('admin.warehouses.update', 'central_wh'), $payload);

        $response->assertRedirect(route('admin.warehouses.index'));
        $this->assertDatabaseHas('locations', [
            'id' => 'central_wh',
            'name_en' => 'Updated Central Logistics Depot',
            'code' => 'LOC-CWH-NEW',
        ]);
    }

    public function test_admin_can_toggle_warehouse_status(): void
    {
        $wh = Location::create([
            'id' => 'wh_test_branch',
            'name_en' => 'Test Branch',
            'name_ar' => 'فرع تجريبي',
            'code' => 'LOC-TST',
            'type' => 'branch',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.warehouses.toggle-status', $wh->id));

        $response->assertSessionHas('status');
        $this->assertFalse($wh->fresh()->is_active);
    }

    public function test_admin_cannot_delete_core_system_hub(): void
    {
        $response = $this->actingAs($this->admin, 'web')->delete(route('admin.warehouses.destroy', 'online'));

        $response->assertSessionHasErrors('delete_error');
        $this->assertDatabaseHas('locations', ['id' => 'online']);
    }

    public function test_admin_cannot_delete_warehouse_with_active_inventory(): void
    {
        $wh = Location::create([
            'id' => 'wh_dammam',
            'name_en' => 'Dammam Warehouse',
            'name_ar' => 'مستودع الدمام',
            'code' => 'LOC-DAM',
            'type' => 'warehouse',
            'is_active' => true,
        ]);

        // Provision inventory with current_stock > 0
        InventoryItem::create([
            'product_id' => $this->product->id,
            'location_id' => 'wh_dammam',
            'location_name_en' => 'Dammam Warehouse',
            'location_name_ar' => 'مستودع الدمام',
            'variant_en' => 'Standard Pack',
            'variant_ar' => 'العبوة القياسية',
            'current_stock' => 25,
            'available_stock' => 25,
            'reserved_stock' => 0,
            'unit_cost' => 50.00,
            'retail_price' => 120.00,
        ]);

        $response = $this->actingAs($this->admin, 'web')->delete(route('admin.warehouses.destroy', 'wh_dammam'));

        $response->assertSessionHasErrors('delete_error');
        $this->assertDatabaseHas('locations', ['id' => 'wh_dammam']);
    }

    public function test_admin_can_delete_empty_custom_warehouse(): void
    {
        $wh = Location::create([
            'id' => 'wh_khobar',
            'name_en' => 'Khobar Temporary Hub',
            'name_ar' => 'مستودع الخبر المؤقت',
            'code' => 'LOC-KHB',
            'type' => 'branch',
            'is_active' => true,
        ]);

        InventoryService::provisionLocationForProducts($wh, initialStock: 0);

        $response = $this->actingAs($this->admin, 'web')->delete(route('admin.warehouses.destroy', 'wh_khobar'));

        $response->assertRedirect(route('admin.warehouses.index'));
        $this->assertDatabaseMissing('locations', ['id' => 'wh_khobar']);
        $this->assertDatabaseMissing('inventory_items', ['location_id' => 'wh_khobar']);
    }
}
