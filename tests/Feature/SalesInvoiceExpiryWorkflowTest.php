<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesInvoiceExpiryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            ['name' => 'Super Admin', 'permissions' => ['*'], 'is_system' => true]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'sales-expiry@bluezone.test'],
            ['name' => 'Sales Admin', 'password' => bcrypt('secret'), 'role_id' => $role->id, 'status' => 'active']
        );

        Location::firstOrCreate(
            ['id' => 'central_wh'],
            ['name_en' => 'Central Warehouse', 'name_ar' => 'المستودع الرئيسي', 'type' => 'warehouse', 'is_active' => true]
        );

        $category = Category::firstOrCreate(
            ['slug' => 'sales-expiry-cat'],
            ['name_en' => 'Bioceuticals', 'name_ar' => 'مكملات']
        );

        $this->product = Product::create([
            'category_id' => $category->id,
            'sku' => 'BZ-PEA-001',
            'slug' => 'bz-pea-001',
            'name_en' => 'Blue PEA Complex',
            'name_ar' => 'بلو بي اي ايه بي كومبلكس',
            'price' => 72.00,
            'cost_price' => 30.00,
            'stock_online' => 100,
            'is_active' => true,
        ]);
    }

    public function test_order_item_auto_resolves_fefo_batch_and_displays_on_sales_invoice_show_and_print(): void
    {
        $earliestExpiry = now()->addMonths(8)->toDateString();
        $laterExpiry = now()->addMonths(20)->toDateString();

        ProductBatch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'LOT-LATER-02',
            'location_id' => 'central_wh',
            'initial_qty' => 50,
            'current_qty' => 50,
            'available_qty' => 50,
            'reserved_qty' => 0,
            'quarantine_qty' => 0,
            'unit_cost' => 30,
            'manufacture_date' => now()->subMonth()->toDateString(),
            'expiry_date' => $laterExpiry,
            'status' => 'approved',
        ]);

        $earliestBatch = ProductBatch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'LOT-FEFO-01',
            'location_id' => 'central_wh',
            'initial_qty' => 50,
            'current_qty' => 50,
            'available_qty' => 50,
            'reserved_qty' => 0,
            'quarantine_qty' => 0,
            'unit_cost' => 30,
            'manufacture_date' => now()->subMonth()->toDateString(),
            'expiry_date' => $earliestExpiry,
            'status' => 'approved',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-TEST-35',
            'invoice_number' => 'INV-B914D7',
            'customer_name' => 'Dr. Nora Al-Sudairy',
            'customer_email' => 'nora@clinic.test',
            'customer_phone' => '+966564448899',
            'date' => now()->toDateString(),
            'status' => 'Confirmed',
            'payment_method' => 'Bank Transfer',
            'payment_status' => 'Pending',
            'subtotal' => 2448.00,
            'discount' => 0,
            'shipping' => 0,
            'tax' => 367.20,
            'total' => 2815.20,
        ]);

        // Simulate creating OrderItem without explicit batch/expiry
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'sku' => 'BZ-PEA-001',
            'unit_price' => 72.00,
            'quantity' => 34,
            'total' => 2448.00,
        ]);

        $this->assertEquals($earliestBatch->id, $orderItem->product_batch_id);
        $this->assertEquals('LOT-FEFO-01', $orderItem->batch_number);
        $this->assertEquals($earliestExpiry, $orderItem->expiry_date->toDateString());

        $showResponse = $this->actingAs($this->admin)->get(route('admin.invoices.show', $order->id));
        $showResponse->assertOk();
        $showResponse->assertSee('Exp: ' . $earliestExpiry . ' / LOT-FEFO-01');

        $printResponse = $this->actingAs($this->admin)->get(route('admin.invoices.print', $order->id));
        $printResponse->assertOk();
        $printResponse->assertSee('Exp: ' . $earliestExpiry . ' / LOT-FEFO-01');
    }

    public function test_legacy_order_item_without_saved_batch_still_displays_active_fefo_expiry_on_print(): void
    {
        $realExpiry = now()->addMonths(14)->toDateString();

        ProductBatch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'LOT-LEGACY-FALLBACK',
            'location_id' => 'central_wh',
            'initial_qty' => 40,
            'current_qty' => 40,
            'available_qty' => 40,
            'reserved_qty' => 0,
            'quarantine_qty' => 0,
            'unit_cost' => 30,
            'manufacture_date' => now()->subMonth()->toDateString(),
            'expiry_date' => $realExpiry,
            'status' => 'approved',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-LEGACY-35',
            'invoice_number' => 'INV-LEGACY-35',
            'customer_name' => 'Legacy Clinic',
            'customer_email' => 'legacy@clinic.test',
            'customer_phone' => '+966500000000',
            'date' => now()->toDateString(),
            'status' => 'Confirmed',
            'payment_method' => 'Cash',
            'payment_status' => 'Paid',
            'subtotal' => 72.00,
            'discount' => 0,
            'shipping' => 0,
            'tax' => 10.80,
            'total' => 82.80,
        ]);

        // Insert directly without triggering model creating hook to simulate legacy row
        OrderItem::withoutEvents(function () use ($order) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $this->product->id,
                'product_name_en' => 'Blue PEA Complex',
                'product_name_ar' => 'بلو بي اي ايه بي كومبلكس',
                'variant_en' => 'Standard Pack',
                'variant_ar' => 'العبوة القياسية',
                'sku' => 'BZ-PEA-001',
                'unit_price' => 72.00,
                'quantity' => 1,
                'total' => 72.00,
                'image' => 'assets/products/blue-mind.jpg',
            ]);
        });

        $printResponse = $this->actingAs($this->admin)->get(route('admin.invoices.print', $order->id));
        $printResponse->assertOk();
        $printResponse->assertSee('Exp: ' . $realExpiry . ' / LOT-LEGACY-FALLBACK');
    }
}
