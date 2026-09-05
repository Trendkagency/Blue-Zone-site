<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryAndOfflineSalesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Product $product;
    protected Category $category;

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

        $this->category = Category::firstOrCreate(
            ['slug' => 'cellular-longevity'],
            ['name_en' => 'Cellular Longevity', 'name_ar' => 'طول العمر الخلوي', 'is_active' => true]
        );

        $this->product = Product::firstOrCreate(
            ['slug' => 'blue-mind'],
            [
                'sku' => 'BZ-MND-001',
                'name_en' => 'BLUE MIND Precision Nootropic',
                'name_ar' => 'بلو مايند',
                'category_id' => $this->category->id,
                'price' => 68.00,
                'cost_price' => 22.00,
                'stock_online' => 100,
                'stock_offline' => 50,
                'low_stock_threshold' => 15,
                'status' => 'active',
            ]
        );

        InventoryService::syncAllProductsInventory();
    }

    public function test_inventory_index_displays_tabs_and_kpis(): void
    {
        // Test in Arabic
        app()->setLocale('ar');
        $responseAr = $this->actingAs($this->admin, 'web')->get(route('admin.inventory.index'));
        $responseAr->assertStatus(200);
        $responseAr->assertSee('جميع المواقع');
        $responseAr->assertSee('المتجر الرئيسي');
        $responseAr->assertSee('إجمالي المخزون الفعلي');

        // Test in English
        app()->setLocale('en');
        $responseEn = $this->actingAs($this->admin, 'web')->get(route('admin.inventory.index'));
        $responseEn->assertStatus(200);
        $responseEn->assertSee('All Locations');
        $responseEn->assertSee('Total Physical Stock');
    }

    public function test_inventory_filtering_by_location_and_status(): void
    {
        // Filter by online location
        $onlineRes = $this->actingAs($this->admin, 'web')->get(route('admin.inventory.index', ['location' => 'online']));
        $onlineRes->assertStatus(200);
        $onlineRes->assertSee('Online Fulfillment Hub');

        // Filter by offline location
        $offlineRes = $this->actingAs($this->admin, 'web')->get(route('admin.inventory.index', ['location' => 'offline']));
        $offlineRes->assertStatus(200);
        $offlineRes->assertSee('Flagship Boutique / POS');

        // Filter by search
        $searchRes = $this->actingAs($this->admin, 'web')->get(route('admin.inventory.index', ['search' => 'BZ-MND-001']));
        $searchRes->assertStatus(200);
        $searchRes->assertSee('BLUE MIND');
    }

    public function test_stock_transfer_between_locations_with_product_sync_and_movement_logged(): void
    {
        $initialOnline = $this->product->stock_online;
        $initialOffline = $this->product->stock_offline;

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.inventory.transfers.store'), [
            'product_id' => $this->product->id,
            'from_location' => 'offline',
            'to_location' => 'online',
            'quantity' => 20,
            'reason' => 'Online flash sale replenishment',
        ]);

        $response->assertRedirect(route('admin.inventory.transfers'));
        $response->assertSessionHas('status');

        $this->product->refresh();
        $this->assertEquals($initialOffline - 20, $this->product->stock_offline);
        $this->assertEquals($initialOnline + 20, $this->product->stock_online);

        // Verify movement audit record
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $this->product->id,
            'movement_type' => 'Stock Transfer',
            'quantity' => 20,
        ]);
    }

    public function test_stock_transfer_rejects_insufficient_stock_or_identical_locations(): void
    {
        // Same location transfer
        $sameRes = $this->actingAs($this->admin, 'web')->post(route('admin.inventory.transfers.store'), [
            'product_id' => $this->product->id,
            'from_location' => 'online',
            'to_location' => 'online',
            'quantity' => 5,
        ]);
        $sameRes->assertSessionHasErrors('transfer_error');

        // Insufficient stock transfer
        $excessRes = $this->actingAs($this->admin, 'web')->post(route('admin.inventory.transfers.store'), [
            'product_id' => $this->product->id,
            'from_location' => 'offline',
            'to_location' => 'online',
            'quantity' => 9999,
        ]);
        $excessRes->assertSessionHasErrors('transfer_error');
    }

    public function test_manual_stock_adjustments(): void
    {
        $initialOnline = $this->product->stock_online;

        // Stock In (+10)
        $inRes = $this->actingAs($this->admin, 'web')->post(route('admin.inventory.adjustments.store'), [
            'product_id' => $this->product->id,
            'location_id' => 'online',
            'movement_type' => 'Stock In',
            'quantity' => 10,
            'reason' => 'Laboratory batch delivery',
        ]);
        $inRes->assertSessionHas('status');
        $this->product->refresh();
        $this->assertEquals($initialOnline + 10, $this->product->stock_online);

        // Damaged (-2)
        $damagedRes = $this->actingAs($this->admin, 'web')->post(route('admin.inventory.adjustments.store'), [
            'product_id' => $this->product->id,
            'location_id' => 'online',
            'movement_type' => 'Damaged',
            'quantity' => 2,
            'reason' => 'Broken seal during handling',
        ]);
        $damagedRes->assertSessionHas('status');
        $this->product->refresh();
        $this->assertEquals($initialOnline + 8, $this->product->stock_online);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $this->product->id,
            'movement_type' => 'Damaged',
            'quantity' => -2,
        ]);
    }

    public function test_offline_sale_pos_creates_order_deducts_offline_stock_and_logs_offline_sale_movement(): void
    {
        $initialOffline = $this->product->stock_offline;

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.offline-sales.store'), [
            'customer_name' => 'Sheikh Fahad Al-Otaibi',
            'customer_phone' => '+966 50 111 2233',
            'payment_method' => 'Apple Pay / Contactless',
            'product_id' => $this->product->id,
            'variant' => 'Standard Pack (60 Caps)',
            'unit_price' => 68.00,
            'quantity' => 3,
            'discount' => 10.00,
        ]);

        $response->assertRedirect(); // Redirects to invoice print

        $this->product->refresh();
        $this->assertEquals($initialOffline - 3, $this->product->stock_offline);

        // Order recorded with discount
        $this->assertDatabaseHas('orders', [
            'channel' => 'offline',
            'customer_name' => 'Sheikh Fahad Al-Otaibi',
            'discount' => 10.00,
            'status' => 'delivered',
            'payment_status' => 'paid',
        ]);

        // Stock movement recorded as Offline Sale
        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $this->product->id,
            'movement_type' => 'Offline Sale',
            'quantity' => -3,
        ]);
    }

    public function test_offline_sale_pos_rejects_quantity_greater_than_available_offline_stock(): void
    {
        $response = $this->actingAs($this->admin, 'web')->post(route('admin.offline-sales.store'), [
            'customer_name' => 'Overdraft Tester',
            'payment_method' => 'Cash Tender',
            'product_id' => $this->product->id,
            'quantity' => 99999,
        ]);

        $response->assertSessionHasErrors('sale_error');
    }

    public function test_inventory_history_ledger_displays_movements(): void
    {
        // Add a movement
        InventoryService::adjustStock(
            product: $this->product,
            locationId: 'online',
            quantityDelta: 15,
            movementType: 'Return',
            reason: 'Customer returned sealed bottle',
            userName: 'Dr. Sarah'
        );

        $response = $this->actingAs($this->admin, 'web')->get(route('admin.inventory.history', ['movement_type' => 'Return']));
        $response->assertStatus(200);
        $response->assertSee('Return');
        $response->assertSee('Customer returned sealed bottle');
    }

    public function test_order_cancellation_restocks_inventory_and_logs_movement(): void
    {
        $initialOnline = $this->product->stock_online;

        $order = Order::create([
            'order_number' => 'BZ-ORD-TEST-099',
            'invoice_number' => 'INV-TEST-099',
            'channel' => 'online',
            'customer_name' => 'Test Patient',
            'date' => now()->toDateString(),
            'status' => 'processing',
            'payment_method' => 'Mada',
            'payment_status' => 'paid',
            'subtotal' => 68.00,
            'discount' => 0.00,
            'shipping' => 0.00,
            'tax' => 10.20,
            'total' => 78.20,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name_en' => $this->product->name_en,
            'product_name_ar' => $this->product->name_ar,
            'sku' => $this->product->sku,
            'unit_price' => 68.00,
            'quantity' => 2,
            'total' => 136.00,
        ]);

        // Cancel the order
        $cancelRes = $this->actingAs($this->admin, 'web')->patch(route('admin.orders.update-status', $order->id), [
            'status' => 'cancelled',
        ]);
        $cancelRes->assertRedirect();

        $this->product->refresh();
        $this->assertEquals($initialOnline + 2, $this->product->stock_online);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $this->product->id,
            'movement_type' => 'Cancelled Order',
            'quantity' => 2,
        ]);
    }

    public function test_product_inventory_show_page_displays_breakdown_and_timeline(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.inventory.show', $this->product->id));
        $response->assertStatus(200);
        $response->assertSee($this->product->name_en);
        $response->assertSee('Online Fulfillment Hub');
        $response->assertSee('Flagship Boutique / POS');
        $response->assertSee('Stock Progression Audit Trail');
    }
}
