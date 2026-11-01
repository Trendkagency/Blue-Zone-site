<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FinancialAccount;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductRecipeItem;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\RawMaterial;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use App\Services\CartService;
use App\Services\FifoAllocationEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductBatchFifoTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(
            ['name' => 'Super Administrator'],
            ['slug' => 'super-admin', 'permissions' => ['*'], 'is_system' => true]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@bluezone.com'],
            ['name' => 'Dr. Admin', 'password' => bcrypt('password'), 'role_id' => $role->id, 'status' => 'active']
        );

        $this->category = Category::create([
            'name_en' => 'Cellular Longevity',
            'name_ar' => 'طول العمر الخلوي',
            'slug' => 'cellular-longevity',
        ]);

        $this->supplier = Supplier::create([
            'code' => 'SUP-TEST-BOM',
            'name_en' => 'Longevity Labs GmbH',
            'name_ar' => 'مختبرات لونجيفيتي الألمانية',
            'email' => 'contact@longevitylabs.de',
            'is_active' => true,
        ]);

        Location::firstOrCreate(
            ['id' => 'online'],
            ['name_en' => 'Online Hub', 'name_ar' => 'مستودع المتجر', 'code' => 'LOC-ONL', 'is_active' => true]
        );

        Location::firstOrCreate(
            ['id' => 'central_wh'],
            ['name_en' => 'Central Depot', 'name_ar' => 'المستودع الرئيسي', 'code' => 'LOC-CWH', 'is_active' => true]
        );
    }

    public function test_product_batch_creation_and_shelf_life_expiry_calculation(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name_en' => 'NMN Resveratrol Cellular Complex',
            'name_ar' => 'مركب إن إم إن ريسفيراترول',
            'slug' => 'nmn-resveratrol-cellular-complex',
            'sku' => 'BZ-NMN-01',
            'price' => 120.00,
            'cost_price' => 45.00,
            'stock_online' => 100,
            'shelf_life_months' => 36,
            'cost_calculation_mode' => 'manual_locked',
            'status' => 'active',
        ]);

        $mfgDate = '2026-06-01';
        $expectedExpiry = '2029-06-01'; // 36 months later
        $this->assertEquals($expectedExpiry, $product->calculateDefaultExpiryDate($mfgDate));

        $batch = ProductBatch::create([
            'batch_number' => ProductBatch::generateBatchNumber($product),
            'product_id' => $product->id,
            'location_id' => 'online',
            'initial_qty' => 50,
            'current_qty' => 50,
            'available_qty' => 50,
            'reserved_qty' => 0,
            'quarantine_qty' => 0,
            'unit_cost' => 45.00,
            'manufacture_date' => $mfgDate,
            'expiry_date' => $expectedExpiry,
            'status' => 'approved',
        ]);

        $this->assertTrue($batch->verifyInvariant());
        $this->assertStringStartsWith('LOT-FG-', $batch->batch_number);
        $this->assertEquals('optimal', $batch->expiry_badge['level']);
    }

    public function test_fifo_allocation_engine_prioritizes_earliest_expiring_batch(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name_en' => 'Spermidine Cellular Renewal',
            'name_ar' => 'سبيرميدين للتجدد الخلوي',
            'slug' => 'spermidine-cellular-renewal',
            'sku' => 'BZ-SPERM-01',
            'price' => 150.00,
            'cost_price' => 50.00,
            'stock_online' => 80,
            'shelf_life_months' => 24,
            'cost_calculation_mode' => 'manual_locked',
            'status' => 'active',
        ]);

        // Batch A: Expiring Later (2028-12-31)
        $batchLater = ProductBatch::create([
            'batch_number' => 'LOT-FG-202611-LATER',
            'product_id' => $product->id,
            'location_id' => 'online',
            'initial_qty' => 30,
            'current_qty' => 30,
            'available_qty' => 30,
            'reserved_qty' => 0,
            'quarantine_qty' => 0,
            'unit_cost' => 50.00,
            'manufacture_date' => '2026-11-01',
            'expiry_date' => '2028-12-31',
            'status' => 'approved',
        ]);

        // Batch B: Expiring Sooner (2027-04-15) -> Should be allocated FIRST by FEFO
        $batchSooner = ProductBatch::create([
            'batch_number' => 'LOT-FG-202611-SOONER',
            'product_id' => $product->id,
            'location_id' => 'online',
            'initial_qty' => 20,
            'current_qty' => 20,
            'available_qty' => 20,
            'reserved_qty' => 0,
            'quarantine_qty' => 0,
            'unit_cost' => 48.00,
            'manufacture_date' => '2025-04-15',
            'expiry_date' => '2027-04-15',
            'status' => 'approved',
        ]);

        $engine = FifoAllocationEngine::getInstance();
        $allocations = $engine->allocateProduct($product, 15, 'online');

        $this->assertCount(1, $allocations);
        $this->assertEquals($batchSooner->id, $allocations[0]['batch_id']);
        $this->assertEquals(15, $allocations[0]['allocated_qty']);

        // Test splitting across batches when required qty exceeds earliest batch
        $allocSplit = $engine->allocateProduct($product, 25, 'online');
        $this->assertCount(2, $allocSplit);
        $this->assertEquals($batchSooner->id, $allocSplit[0]['batch_id']);
        $this->assertEquals(20, $allocSplit[0]['allocated_qty']);
        $this->assertEquals($batchLater->id, $allocSplit[1]['batch_id']);
        $this->assertEquals(5, $allocSplit[1]['allocated_qty']);
    }

    public function test_fifo_allocation_engine_blocks_expired_batches(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name_en' => 'Quercetin Phytosome',
            'name_ar' => 'كيرسيتين فيتوسوم',
            'slug' => 'quercetin-phytosome',
            'sku' => 'BZ-QUERC-01',
            'price' => 80.00,
            'cost_price' => 25.00,
            'stock_online' => 10,
            'shelf_life_months' => 24,
            'cost_calculation_mode' => 'manual_locked',
            'status' => 'active',
        ]);

        // Expired Batch
        ProductBatch::create([
            'batch_number' => 'LOT-FG-EXPIRED-99',
            'product_id' => $product->id,
            'location_id' => 'online',
            'initial_qty' => 50,
            'current_qty' => 50,
            'available_qty' => 50,
            'reserved_qty' => 0,
            'quarantine_qty' => 0,
            'unit_cost' => 25.00,
            'manufacture_date' => '2022-01-01',
            'expiry_date' => '2024-01-01', // Past date
            'status' => 'approved',
        ]);

        $this->expectException(\DomainException::class);
        FifoAllocationEngine::getInstance()->allocateProduct($product, 10, 'online');
    }

    public function test_dynamic_bom_cost_rollup_on_raw_material_price_change(): void
    {
        $rawA = RawMaterial::create([
            'code' => 'RM-NMN-PURE',
            'name_en' => 'Ultra Pure NMN 99.9%',
            'name_ar' => 'إن إم إن نقي 99.9%',
            'standard_cost' => 10.00,
            'unit_of_measure' => 'g',
            'is_active' => true,
        ]);

        $rawB = RawMaterial::create([
            'code' => 'RM-RESV-TRANS',
            'name_en' => 'Trans-Resveratrol Micronized',
            'name_ar' => 'ترانس ريسفيراترول دقيق',
            'standard_cost' => 5.00,
            'unit_of_measure' => 'g',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $this->category->id,
            'name_en' => 'Cellular NAD+ Synergy Formula',
            'name_ar' => 'تركيبة تعزيز ناد+ الخلوية',
            'slug' => 'cellular-nad-synergy-formula',
            'sku' => 'BZ-NAD-BOM',
            'price' => 95.00,
            'cost_price' => 0.00,
            'packaging_overhead_cost' => 3.50,
            'labor_overhead_cost' => 2.50,
            'cost_calculation_mode' => 'auto_bom',
            'stock_online' => 50,
            'status' => 'active',
        ]);

        // Recipe: 2 units of RawA (cost 20) + 1 unit of RawB (cost 5) = 25 raw material cost
        ProductRecipeItem::create([
            'product_id' => $product->id,
            'raw_material_id' => $rawA->id,
            'quantity_per_unit' => 2.0,
            'loss_percentage' => 0.0,
        ]);

        ProductRecipeItem::create([
            'product_id' => $product->id,
            'raw_material_id' => $rawB->id,
            'quantity_per_unit' => 1.0,
            'loss_percentage' => 0.0,
        ]);

        // Initial Rollup: (2*10) + (1*5) + 3.50 + 2.50 = 31.00
        $product->refresh();
        $this->assertEquals(31.00, (float) $product->cost_price);

        // When Raw Material A cost increases from 10.00 to 15.00:
        // Expected new cost: (2*15) + (1*5) + 3.50 + 2.50 = 41.00
        $rawA->update(['standard_cost' => 15.00]);

        $product->refresh();
        $this->assertEquals(41.00, (float) $product->cost_price);
    }

    public function test_manual_locked_cost_mode_ignores_raw_material_price_changes(): void
    {
        $raw = RawMaterial::create([
            'code' => 'RM-APIGENIN',
            'name_en' => 'Apigenin Extract 98%',
            'name_ar' => 'خلاصة الأبيجينين 98%',
            'standard_cost' => 12.00,
            'unit_of_measure' => 'g',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $this->category->id,
            'name_en' => 'Apigenin Night Sleep Protocol',
            'name_ar' => 'بروتوكول النوم بالأبيجينين',
            'slug' => 'apigenin-night-sleep-protocol',
            'sku' => 'BZ-APIG-01',
            'price' => 75.00,
            'cost_price' => 28.00, // Fixed manual locked cost
            'packaging_overhead_cost' => 2.00,
            'labor_overhead_cost' => 1.00,
            'cost_calculation_mode' => 'manual_locked', // Locked!
            'stock_online' => 30,
            'status' => 'active',
        ]);

        ProductRecipeItem::create([
            'product_id' => $product->id,
            'raw_material_id' => $raw->id,
            'quantity_per_unit' => 2.0,
            'loss_percentage' => 0.0,
        ]);

        // Raw material changes from 12.00 to 50.00
        $raw->update(['standard_cost' => 50.00]);

        // Product cost_price must remain 28.00 because it's manual_locked
        $product->refresh();
        $this->assertEquals(28.00, (float) $product->cost_price);
    }

    public function test_purchase_invoice_can_contain_both_products_and_raw_materials(): void
    {
        $raw = RawMaterial::create([
            'code' => 'RM-BULK-01',
            'name_en' => 'Pure CoQ10 Ubiquinol',
            'name_ar' => 'يوبيكوينول نقي',
            'standard_cost' => 30.00,
            'unit_of_measure' => 'kg',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $this->category->id,
            'name_en' => 'Mitochondrial CoQ10 Complex',
            'name_ar' => 'مركب الميتوكوندريا كو كيو 10',
            'slug' => 'mitochondrial-coq10-complex',
            'sku' => 'BZ-COQ10-01',
            'price' => 110.00,
            'cost_price' => 35.00,
            'stock_online' => 20,
            'status' => 'active',
        ]);

        $payload = [
            'supplier_id' => $this->supplier->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'reference_number' => 'SUP-BILL-HYBRID-001',
            'subtotal' => 1350.00,
            'tax_amount' => 202.50,
            'grand_total' => 1552.50,
            'items' => [
                [
                    'item_type' => 'raw_material',
                    'raw_material_id' => $raw->id,
                    'item_name' => 'Raw CoQ10 10kg Drum',
                    'quantity' => 10,
                    'unit_price' => 100.00,
                    'tax_rate' => 15.0,
                    'batch_number' => 'LOT-RM-2026-901',
                    'expiry_date' => '2028-10-01',
                ],
                [
                    'item_type' => 'product',
                    'product_id' => $product->id,
                    'item_name' => 'Finished CoQ10 Boxes',
                    'quantity' => 10,
                    'unit_price' => 35.00,
                    'tax_rate' => 15.0,
                    'batch_number' => 'LOT-FG-2026-802',
                    'expiry_date' => '2029-05-15',
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.finance.purchase-invoices.store'), $payload);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $invoice = PurchaseInvoice::with('items')->latest('id')->first();
        $this->assertNotNull($invoice);
        $this->assertCount(2, $invoice->items);

        $rmItem = $invoice->items->firstWhere('item_type', 'raw_material');
        $this->assertNotNull($rmItem);
        $this->assertEquals($raw->id, $rmItem->raw_material_id);
        $this->assertEquals('LOT-RM-2026-901', $rmItem->batch_number);
        $this->assertEquals('2028-10-01', $rmItem->expiry_date->toDateString());

        $fgItem = $invoice->items->firstWhere('item_type', 'product');
        $this->assertNotNull($fgItem);
        $this->assertEquals($product->id, $fgItem->product_id);
        $this->assertEquals('LOT-FG-2026-802', $fgItem->batch_number);
        $this->assertEquals('2029-05-15', $fgItem->expiry_date->toDateString());
    }

    public function test_customer_checkout_allocates_fefo_batch_and_links_to_order_item(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name_en' => 'Fisetin Longevity Activator',
            'name_ar' => 'منشط فيسيتين الخلوي',
            'slug' => 'fisetin-longevity-activator',
            'sku' => 'BZ-FISETIN-01',
            'price' => 90.00,
            'cost_price' => 30.00,
            'stock_online' => 50,
            'shelf_life_months' => 24,
            'status' => 'active',
        ]);

        $batch = ProductBatch::create([
            'batch_number' => 'LOT-FG-202611-FISETIN',
            'product_id' => $product->id,
            'location_id' => 'online',
            'initial_qty' => 50,
            'current_qty' => 50,
            'available_qty' => 50,
            'reserved_qty' => 0,
            'quarantine_qty' => 0,
            'unit_cost' => 30.00,
            'manufacture_date' => '2026-11-01',
            'expiry_date' => '2028-11-01',
            'status' => 'approved',
        ]);

        // Add to cart session using CartService
        CartService::add($product, 2);

        $checkoutData = [
            'full_name' => 'Dr. Sami Al-Husseini',
            'email' => 'sami@clinic.com',
            'phone' => '+966551234567',
            'address' => 'Olaya St 102',
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
            'postal_code' => '12211',
            'payment_method' => 'cod',
        ];

        $response = $this->post(route('customer.checkout.store'), $checkoutData);
        $response->assertSessionHasNoErrors();

        $order = Order::with('items')->latest('id')->first();
        $this->assertNotNull($order);
        $this->assertCount(1, $order->items);

        $orderItem = $order->items->first();
        $this->assertEquals($batch->id, $orderItem->product_batch_id);
        $this->assertEquals('LOT-FG-202611-FISETIN', $orderItem->batch_number);
        $this->assertEquals('2028-11-01', $orderItem->expiry_date->toDateString());

        // Batch available quantity should be deducted from 50 to 48
        $batch->refresh();
        $this->assertEquals(48, $batch->available_qty);
        $this->assertEquals(48, $batch->current_qty);
    }

    public function test_product_store_with_initial_batch_and_shelf_life_provisions_product_batch(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin_batch@bluezone.com'],
            ['name' => 'Admin Batch User', 'password' => bcrypt('password'), 'role_id' => 1, 'status' => 'active']
        );

        $payload = [
            'sku' => 'BZ-BATCH-001',
            'barcode' => '628100998877',
            'category_id' => $this->category->id,
            'brand' => 'Blue Zone Bioceuticals',
            'name_en' => 'Spermidine Longevity Matrix',
            'name_ar' => 'مصفوفة سبيرميدين الخلوية',
            'price' => 120.00,
            'cost_price' => 45.00,
            'stock_online' => 75,
            'low_stock_threshold' => 10,
            'shelf_life_months' => 36,
            'initial_batch_number' => 'LOT-FG-202611-SPERM01',
            'manufacture_date' => '2026-11-01',
            'expiry_date' => '2029-11-01',
            'batch_status' => 'approved',
            'status' => 'active',
        ];

        $response = $this->actingAs($admin, 'web')->post(route('admin.products.store'), $payload);
        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', 'BZ-BATCH-001')->first();
        $this->assertNotNull($product);
        $this->assertEquals(36, $product->shelf_life_months);
        $this->assertEquals(75, $product->stock_online);

        $batch = ProductBatch::where('product_id', $product->id)->first();
        $this->assertNotNull($batch);
        $this->assertEquals('LOT-FG-202611-SPERM01', $batch->batch_number);
        $this->assertEquals(75, $batch->initial_qty);
        $this->assertEquals(75, $batch->current_qty);
        $this->assertEquals(75, $batch->available_qty);
        $this->assertEquals(0, $batch->quarantine_qty);
        $this->assertEquals(45.00, (float) $batch->unit_cost);
        $this->assertEquals('2026-11-01', $batch->manufacture_date->toDateString());
        $this->assertEquals('2029-11-01', $batch->expiry_date->toDateString());
        $this->assertEquals('approved', $batch->status);
    }
}
