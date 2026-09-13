<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPaginationTest extends TestCase
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
    }

    public function test_products_pagination_generates_working_links_and_preserves_query(): void
    {
        $category = Category::firstOrCreate(
            ['slug' => 'test-cat'],
            ['name_en' => 'Test Cat', 'name_ar' => 'تصنيف تجريبي', 'is_active' => true]
        );

        // Create 35 products to force 3 pages (15 per page)
        for ($i = 1; $i <= 35; $i++) {
            Product::create([
                'sku' => "SKU-PAG-{$i}",
                'slug' => "test-product-{$i}",
                'category_id' => $category->id,
                'brand' => 'Blue Zone',
                'name_en' => "Test Product {$i}",
                'name_ar' => "منتج تجريبي {$i}",
                'price' => 100 + $i,
                'stock_online' => 10,
                'stock_offline' => 5,
                'status' => 'active',
            ]);
        }

        // Test Page 1
        $res1 = $this->actingAs($this->admin, 'web')->get(route('admin.products.index', ['status' => 'active']));
        $res1->assertStatus(200);
        $res1->assertSee('SKU-PAG-1');
        $res1->assertSee('page=2');
        $res1->assertSee('status=active');

        // Test Page 2
        $res2 = $this->actingAs($this->admin, 'web')->get(route('admin.products.index', ['status' => 'active', 'page' => 2]));
        $res2->assertStatus(200);
        $res2->assertSee('page=1');
        $res2->assertSee('page=3');

        // Test Page 3
        $res3 = $this->actingAs($this->admin, 'web')->get(route('admin.products.index', ['status' => 'active', 'page' => 3]));
        $res3->assertStatus(200);
    }

    public function test_customers_pagination_renders_properly(): void
    {
        for ($i = 1; $i <= 25; $i++) {
            Customer::create([
                'name' => "Customer {$i}",
                'email' => "customer{$i}@test.com",
                'phone' => "+9665000000{$i}",
                'status' => 'active',
            ]);
        }

        $res = $this->actingAs($this->admin, 'web')->get(route('admin.customers.index'));
        $res->assertStatus(200);
        $res->assertSee('page=2');
    }

    public function test_orders_pagination_preserves_channel_and_status(): void
    {
        $customer = Customer::create([
            'name' => 'John Doe',
            'email' => 'john@test.com',
        ]);

        for ($i = 1; $i <= 20; $i++) {
            Order::create([
                'order_number' => "ORD-PAG-{$i}",
                'invoice_number' => "INV-PAG-{$i}",
                'customer_name' => 'John Doe',
                'customer_email' => 'john@test.com',
                'channel' => 'online',
                'status' => 'pending',
                'payment_status' => 'paid',
                'total' => 150.00,
                'date' => now()->toDateString(),
            ]);
        }

        $res = $this->actingAs($this->admin, 'web')->get(route('admin.orders.index', ['channel' => 'online']));
        $res->assertStatus(200);
        $res->assertSee('ORD-PAG-1');
        $res->assertSee('page=2');
        $res->assertSee('channel=online');
    }
}
