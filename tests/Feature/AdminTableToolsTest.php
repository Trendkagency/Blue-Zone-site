<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTableToolsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

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

        $category = Category::firstOrCreate(
            ['slug' => 'supplements'],
            ['name_en' => 'Longevity Supplements', 'name_ar' => 'مكملات طول العمر', 'is_active' => true]
        );

        Product::create([
            'sku' => 'BZ-TAB-001',
            'slug' => 'table-test-product',
            'category_id' => $category->id,
            'brand' => 'Blue Zone',
            'name_en' => 'Cellular Restorative Bioceutical',
            'name_ar' => 'مركب التجديد الخلوي',
            'price' => 200.00,
            'cost_price' => 80.00,
            'stock_online' => 30,
            'stock_offline' => 15,
            'low_stock_threshold' => 10,
            'status' => 'active',
        ]);

        InventoryService::syncAllProductsInventory();
    }

    public function test_admin_table_tools_script_is_accessible(): void
    {
        $this->assertFileExists(public_path('js/admin-table-tools.js'));
        $content = file_get_contents(public_path('js/admin-table-tools.js'));
        $this->assertStringContainsString('BlueZoneTableEngine', $content);
        $this->assertStringContainsString('exportToExcel', $content);
        $this->assertStringContainsString('exportToCsv', $content);
        $this->assertStringContainsString('printTable', $content);
    }

    public function test_products_index_renders_with_table_tools_and_sorting(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertSee('data-export-excel="#productsTable"', false);
        $response->assertSee('data-print-table="#productsTable"', false);
        $response->assertSee('data-table-filter="#productsTable"', false);
        $response->assertSee('admin-table-tools.js');
    }

    public function test_orders_index_renders_with_table_tools(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.orders.index'));

        $response->assertStatus(200);
        $response->assertSee('data-export-excel="#ordersTable"', false);
        $response->assertSee('data-print-table="#ordersTable"', false);
    }

    public function test_inventory_control_renders_with_table_tools(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.inventory.control'));

        $response->assertStatus(200);
        $response->assertSee('data-export-excel="#controlProductsTable"', false);
        $response->assertSee('data-print-table="#controlProductsTable"', false);
    }

    public function test_inventory_history_renders_with_table_tools(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.inventory.history'));

        $response->assertStatus(200);
        $response->assertSee('data-export-excel="#inventoryHistoryTable"', false);
        $response->assertSee('data-print-table="#inventoryHistoryTable"', false);
    }

    public function test_customers_index_renders_with_table_tools(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.customers.index'));

        $response->assertStatus(200);
        $response->assertSee('data-export-excel="#customersTable"', false);
        $response->assertSee('data-print-table="#customersTable"', false);
    }

    public function test_offline_sales_index_renders_with_table_tools(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.offline-sales.index'));

        $response->assertStatus(200);
        $response->assertSee('data-export-excel="#offlineSalesTable"', false);
        $response->assertSee('data-print-table="#offlineSalesTable"', false);
    }
}
