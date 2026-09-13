<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInventoryControlTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Product $product;

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
            ['slug' => 'perfumes'],
            ['name_en' => 'Perfumes', 'name_ar' => 'العطور', 'is_active' => true]
        );

        $this->product = Product::create([
            'sku' => 'BPD-101',
            'slug' => 'blue-perfume-deluxe',
            'category_id' => $category->id,
            'brand' => 'Blue Zone',
            'name_en' => 'Blue Perfume Deluxe',
            'name_ar' => 'عطر بلو ديلوكس',
            'price' => 150.00,
            'cost_price' => 50.00,
            'stock_online' => 20,
            'stock_offline' => 10,
            'low_stock_threshold' => 5,
            'status' => 'active',
        ]);

        InventoryService::syncAllProductsInventory();
    }

    public function test_admin_can_access_inventory_control_page(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.inventory.control'));

        $response->assertStatus(200);
        $response->assertSee('BPD-101');
        $response->assertSee('Blue Perfume Deluxe');
    }

    public function test_admin_can_quick_adjust_stock_increase(): void
    {
        $response = $this->actingAs($this->admin, 'web')->postJson(route('admin.inventory.quick-adjust'), [
            'product_id' => $this->product->id,
            'location_id' => 'online',
            'action' => 'increase',
            'movement_type' => 'Stock In',
            'quantity' => 5,
            'reason' => 'Weekly replenishment shipment',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'product' => [
                'id' => $this->product->id,
                'stock_online' => 25,
                'total_stock' => 85,
            ],
        ]);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $this->product->id,
            'movement_type' => 'Stock In',
            'quantity' => 5,
        ]);
    }

    public function test_admin_can_quick_adjust_stock_decrease(): void
    {
        $response = $this->actingAs($this->admin, 'web')->postJson(route('admin.inventory.quick-adjust'), [
            'product_id' => $this->product->id,
            'location_id' => 'offline',
            'action' => 'decrease',
            'movement_type' => 'Damaged',
            'quantity' => 2,
            'reason' => 'Damaged bottle during shelf restocking',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'product' => [
                'id' => $this->product->id,
                'stock_offline' => 8,
            ],
        ]);
    }

    public function test_admin_can_batch_adjust_multiple_products(): void
    {
        $category = Category::first();
        $product2 = Product::create([
            'sku' => 'OUD-202',
            'slug' => 'oud-royal',
            'category_id' => $category->id,
            'brand' => 'Blue Zone',
            'name_en' => 'Oud Royal',
            'name_ar' => 'عود رويال',
            'price' => 250.00,
            'cost_price' => 100.00,
            'stock_online' => 15,
            'stock_offline' => 5,
            'low_stock_threshold' => 5,
            'status' => 'active',
        ]);

        InventoryService::syncAllProductsInventory();

        $response = $this->actingAs($this->admin, 'web')->postJson(route('admin.inventory.batch-adjust'), [
            'product_ids' => [$this->product->id, $product2->id],
            'location_id' => 'central_wh',
            'movement_type' => 'Stock In',
            'quantity' => 10,
            'reason' => 'Batch restock from factory',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertCount(2, $response->json('products'));
    }
}
