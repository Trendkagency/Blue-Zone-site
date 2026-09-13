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

class StockAllocatorTest extends TestCase
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
            ['slug' => 'cellular-longevity'],
            ['name_en' => 'Cellular Longevity', 'name_ar' => 'طول العمر الخلوي', 'is_active' => true]
        );

        $this->product = Product::create([
            'sku' => 'BZ-ALLOC-001',
            'slug' => 'allocator-test-product',
            'category_id' => $category->id,
            'brand' => 'Blue Zone Bioceuticals',
            'name_en' => 'Allocator Bioceutical',
            'name_ar' => 'تركيبة التوزيع الحيوية',
            'price' => 120.00,
            'cost_price' => 40.00,
            'stock_online' => 60,
            'stock_offline' => 30,
            'low_stock_threshold' => 10,
            'status' => 'active',
        ]);

        InventoryService::syncAllProductsInventory();
    }

    public function test_allocator_workspace_renders_with_multi_hub_kanban_and_kpis(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.inventory.allocator'));

        $response->assertStatus(200);
        $response->assertSee('hub_online');
        $response->assertSee('hub_offline');
        $response->assertSee('hub_central_wh');
        $response->assertSee('Allocator Bioceutical');
        $response->assertSee('BZ-ALLOC-001');
    }

    public function test_ajax_transfer_moves_stock_between_hubs_and_returns_live_kpis(): void
    {
        $payload = [
            'product_id' => $this->product->id,
            'from_location' => 'online',
            'to_location' => 'offline',
            'quantity' => 15,
            'reason' => 'Live Drag & Drop Test',
        ];

        $response = $this->actingAs($this->admin, 'web')->postJson(route('admin.inventory.allocator.transfer'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'product' => [
                'id' => $this->product->id,
                'stock_online' => 45,
                'stock_offline' => 45,
            ],
        ]);

        $this->product->refresh();
        $this->assertEquals(45, $this->product->stock_online);
        $this->assertEquals(45, $this->product->stock_offline);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $this->product->id,
            'quantity' => 15,
            'movement_type' => 'Stock Transfer',
        ]);
    }

    public function test_ajax_transfer_validates_insufficient_stock(): void
    {
        $payload = [
            'product_id' => $this->product->id,
            'from_location' => 'offline',
            'to_location' => 'online',
            'quantity' => 9999, // Exceeds available stock (30)
        ];

        $response = $this->actingAs($this->admin, 'web')->postJson(route('admin.inventory.allocator.transfer'), $payload);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_ajax_batch_split_rebalances_selected_products_by_ratio(): void
    {
        // Total stock for this product = 60 (online) + 30 (offline) + 50 (central default) = 140
        $payload = [
            'product_ids' => [$this->product->id],
            'online_pct' => 50,
            'offline_pct' => 30,
            'central_pct' => 20,
            'reason' => 'Batch 50/30/20 rebalancing',
        ];

        $response = $this->actingAs($this->admin, 'web')->postJson(route('admin.inventory.allocator.batch_split'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->product->refresh();
        // 50% of 140 = 70, 30% of 140 = 42, 20% of 140 = 28
        $this->assertEquals(70, $this->product->stock_online);
        $this->assertEquals(42, $this->product->stock_offline);

        $centralItem = InventoryItem::where('product_id', $this->product->id)->where('location_id', 'central_wh')->first();
        $this->assertEquals(28, $centralItem->current_stock);
    }
}
