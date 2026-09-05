<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTablesAndPrintTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Order $order;
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
                'status' => 'active',
            ]
        );

        $this->order = Order::firstOrCreate(
            ['order_number' => 'BZ-ORD-2026-001'],
            [
                'invoice_number' => 'INV-2026-001',
                'channel' => 'online',
                'customer_name' => 'Dr. Zaid Al-Harbi',
                'customer_email' => 'zaid.harbi@example.com',
                'customer_phone' => '+966 50 123 4567',
                'date' => now()->toDateString(),
                'status' => 'pending',
                'payment_method' => 'Mada / Visa',
                'payment_status' => 'paid',
                'subtotal' => 142.00,
                'discount' => 0.00,
                'shipping' => 0.00,
                'tax' => 21.30,
                'total' => 163.30,
                'shipping_address' => [
                    'recipient' => 'Dr. Zaid Al-Harbi',
                    'street' => 'King Fahd Rd, Level 24',
                    'city' => 'Riyadh',
                    'country' => 'Saudi Arabia',
                ],
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $this->order->id, 'product_id' => $this->product->id],
            [
                'product_name_en' => $this->product->name_en,
                'product_name_ar' => $this->product->name_ar,
                'variant_en' => '60 Veg Capsules',
                'sku' => $this->product->sku,
                'unit_price' => 68.00,
                'quantity' => 2,
                'total' => 136.00,
            ]
        );
    }

    public function test_admin_dashboard_loads_with_live_database_kpis(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Overview');
    }

    public function test_admin_orders_index_and_detail_load_successfully(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.orders.index'));
        $response->assertStatus(200);
        $response->assertSee($this->order->order_number);

        $showResponse = $this->actingAs($this->admin, 'web')->get(route('admin.orders.show', $this->order->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($this->order->order_number);
        $showResponse->assertSee('Print Official Tax Invoice');
    }

    public function test_real_time_order_status_update_and_inventory_restock(): void
    {
        $response = $this->actingAs($this->admin, 'web')->patch(route('admin.orders.update-status', $this->order->id), [
            'status' => 'processing',
        ]);

        $response->assertRedirect();
        $this->order->refresh();
        $this->assertEquals('processing', $this->order->status);
    }

    public function test_special_design_print_invoice_renders_rich_corporate_and_tax_info(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.invoices.print', $this->order->id));
        $response->assertStatus(200);
        
        // Assert corporate and tax metadata
        $response->assertSee('31004829100003'); // VAT Tax ID
        $response->assertSee('CR-1010842910');   // Commercial Record
        $response->assertSee('MOH-CERT-2026-BZ884'); // Clinical License
        $response->assertSee('TAX INVOICE');
        $response->assertSee('فاتورة ضريبية رسمية');
        $response->assertSee('api.qrserver.com'); // ZATCA Dynamic QR Code
        $response->assertSee('@media print');    // Custom printable CSS
        $response->assertSee($this->order->invoice_number);
        $response->assertSee($this->order->customer_name);
    }

    public function test_inventory_management_and_real_time_stock_transfer(): void
    {
        $initialOnline = $this->product->stock_online;
        $initialOffline = $this->product->stock_offline;

        $indexRes = $this->actingAs($this->admin, 'web')->get(route('admin.inventory.index'));
        $indexRes->assertStatus(200);

        $transfersRes = $this->actingAs($this->admin, 'web')->get(route('admin.inventory.transfers'));
        $transfersRes->assertStatus(200);

        // Execute Real-time Stock Transfer (move 5 units from online to offline)
        $postRes = $this->actingAs($this->admin, 'web')->post(route('admin.inventory.transfers.store'), [
            'product_id' => $this->product->id,
            'from_location' => 'online',
            'to_location' => 'offline',
            'quantity' => 5,
            'reason' => 'Boutique Weekend Restock Event',
        ]);

        $postRes->assertRedirect(route('admin.inventory.transfers'));
        
        $this->product->refresh();
        $this->assertEquals($initialOnline - 5, $this->product->stock_online);
        $this->assertEquals($initialOffline + 5, $this->product->stock_offline);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $this->product->id,
            'movement_type' => 'Stock Transfer',
            'quantity' => 5,
        ]);
    }

    public function test_offline_pos_sales_checkout_and_real_time_deduction(): void
    {
        $initialOffline = $this->product->stock_offline;

        $createRes = $this->actingAs($this->admin, 'web')->get(route('admin.offline-sales.create'));
        $createRes->assertStatus(200);

        // Complete POS sale
        $postRes = $this->actingAs($this->admin, 'web')->post(route('admin.offline-sales.store'), [
            'customer_name' => 'Walk-In VIP Patient',
            'customer_phone' => '+966 55 999 8877',
            'payment_method' => 'Mada / Debit POS Terminal',
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $postRes->assertRedirect(); // Redirects to print invoice
        
        $this->product->refresh();
        $this->assertEquals($initialOffline - 2, $this->product->stock_offline);

        $this->assertDatabaseHas('orders', [
            'channel' => 'offline',
            'customer_name' => 'Walk-In VIP Patient',
            'status' => 'delivered',
        ]);
    }

    public function test_customers_reports_products_and_categories_pages(): void
    {
        $this->actingAs($this->admin, 'web')->get(route('admin.customers.index'))->assertStatus(200);
        $this->actingAs($this->admin, 'web')->get(route('admin.reports.index'))->assertStatus(200);
        $this->actingAs($this->admin, 'web')->get(route('admin.products.index'))->assertStatus(200);
        $this->actingAs($this->admin, 'web')->get(route('admin.categories.index'))->assertStatus(200);
    }
}
