<?php

namespace Tests\Feature;

use App\Enums\RawMaterialBatchStatus;
use App\Enums\RawMaterialCategory;
use App\Enums\StorageCondition;
use App\Enums\SupplierType;
use App\Enums\UnitOfMeasure;
use App\Enums\WorkOrderStatus;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\RawMaterial;
use App\Models\RawMaterialBatch;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\FinanceService;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ExpiryWorkflowEndToEndTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create([
            'name' => 'Super Admin',
            'permissions' => ['*'],
        ]);

        $this->adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'status' => 'active',
        ]);

        Location::updateOrCreate(['id' => 'online'], [
            'name_en' => 'Online Fulfillment Hub',
            'name_ar' => 'مستودع المتجر الإلكتروني',
            'code' => 'LOC-ONL',
            'is_active' => true,
        ]);

        Location::updateOrCreate(['id' => 'central_wh'], [
            'name_en' => 'Central Quarantine Warehouse',
            'name_ar' => 'المستودع المركزي الرئيسي',
            'code' => 'LOC-CWH',
            'is_active' => true,
        ]);
    }

    protected function createTestProduct(string $sku = 'BZ-EXP-E2E-1', int $stockOnline = 0): Product
    {
        $category = Category::firstOrCreate(
            ['slug' => 'cellular-longevity'],
            ['name_en' => 'Cellular Longevity', 'name_ar' => 'إطالة العمر الخلوي']
        );

        return Product::create([
            'sku' => $sku,
            'slug' => strtolower($sku) . '-' . uniqid(),
            'name_en' => 'Longevity Protocol',
            'name_ar' => 'بروتوكول إطالة العمر',
            'category_id' => $category->id,
            'price' => 100.00,
            'cost_price' => 40.00,
            'stock_online' => $stockOnline,
            'shelf_life_months' => 24,
            'status' => 'active',
        ]);
    }

    public function test_purchase_invoice_work_order_and_transfer_preserve_batch_expiry_dates(): void
    {
        $this->actingAs($this->adminUser);
        $product = $this->createTestProduct('BZ-PI-WO-01', 0);

        $supplier = Supplier::create([
            'code' => 'SUP-EXP-01',
            'type' => SupplierType::IngredientSupplier,
            'name_en' => 'Global Pharma Labs',
            'name_ar' => 'معامل الأدوية العالمية',
            'email' => 'global.pharma@bluezone.test',
            'is_active' => true,
        ]);

        // 1. Direct Finished Product Purchase Invoice creates ProductBatch AND syncs InventoryItem
        $financeService = app(FinanceService::class);
        $piExpiry = now()->addMonths(10)->toDateString();

        $financeService->createPurchaseInvoice([
            'supplier_id' => $supplier->id,
            'invoice_date' => now()->toDateString(),
            'subtotal' => 400.00,
            'tax_amount' => 60.00,
            'grand_total' => 460.00,
            'items' => [
                [
                    'item_type' => 'product',
                    'product_id' => $product->id,
                    'batch_number' => 'LOT-SUP-EARLY',
                    'expiry_date' => $piExpiry,
                    'quantity' => 10,
                    'unit_price' => 40.00,
                ],
            ],
        ]);

        $this->assertDatabaseHas('product_batches', [
            'product_id' => $product->id,
            'batch_number' => 'LOT-SUP-EARLY',
            'location_id' => 'central_wh',
            'available_qty' => 10,
        ]);
        $centralItem = InventoryService::getItem($product, 'central_wh');
        $this->assertEquals(10, $centralItem->current_stock);

        // 2. Manufacturing Work Order Completion accepts custom Manufacture & Expiry dates cleanly
        $wo = WorkOrder::create([
            'product_id' => $product->id,
            'target_finished_lot' => 'LOT-FG-MFG-01',
            'planned_qty' => 20,
            'status' => WorkOrderStatus::InProduction,
            'location_id' => 'central_wh',
            'scheduled_start' => now(),
            'created_by' => $this->adminUser->id,
        ]);

        $woMfg = now()->subDays(2)->toDateString();
        $woExpiry = now()->addMonths(20)->toDateString();

        $this->post(route('admin.work-orders.complete', $wo), [
            'produced_qty' => 20,
            'rejected_qty' => 0,
            'manufacture_date' => $woMfg,
            'expiry_date' => $woExpiry,
        ])->assertRedirect();

        $woBatch = ProductBatch::where('work_order_id', $wo->id)->firstOrFail();
        $this->assertEquals($woExpiry, $woBatch->expiry_date->toDateString());
        $this->assertEquals($woMfg, $woBatch->manufacture_date->toDateString());
        $this->assertEquals(20, $woBatch->available_qty);

        // 3. Hub transfer (partial qty from central_wh -> online) moves FEFO batch & preserves earliest expiry
        InventoryService::transferStock($product->fresh(), 'central_wh', 'online', 6, 'Replenish E-Commerce', 'Admin');

        // LOT-SUP-EARLY had earliest expiry ($piExpiry) & qty 10 -> split: 4 stay central_wh, 6 child batch in online
        $sourceBatch = ProductBatch::where('batch_number', 'LOT-SUP-EARLY')->firstOrFail();
        $this->assertEquals('central_wh', $sourceBatch->location_id);
        $this->assertEquals(4, $sourceBatch->available_qty);

        $onlineBatch = ProductBatch::where('product_id', $product->id)
            ->where('location_id', 'online')
            ->firstOrFail();
        $this->assertEquals(6, $onlineBatch->available_qty);
        $this->assertEquals($piExpiry, $onlineBatch->expiry_date->toDateString());
        $this->assertStringStartsWith('LOT-SUP-EARLY-ONL', $onlineBatch->batch_number);
    }

    public function test_quotation_conversion_and_order_cancellation_roundtrip_fefo_batches(): void
    {
        $this->actingAs($this->adminUser);
        $product = $this->createTestProduct('BZ-QT-01', 0);

        $earlierExp = now()->addMonths(5)->toDateString();
        $laterExp = now()->addMonths(18)->toDateString();

        $b1 = ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => 'LOT-QT-FEFO-1',
            'location_id' => 'central_wh',
            'initial_qty' => 5,
            'current_qty' => 5,
            'available_qty' => 5,
            'manufacture_date' => now()->subMonth()->toDateString(),
            'expiry_date' => $earlierExp,
            'status' => 'approved',
        ]);

        ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => 'LOT-QT-FEFO-2',
            'location_id' => 'central_wh',
            'initial_qty' => 10,
            'current_qty' => 10,
            'available_qty' => 10,
            'manufacture_date' => now()->toDateString(),
            'expiry_date' => $laterExp,
            'status' => 'approved',
        ]);

        InventoryService::adjustStock($product, 'central_wh', 15, 'Stock In', 'Seed hubs', 'Admin', 'Init', false);

        $customer = Customer::create([
            'name' => 'Clinic Buyer',
            'email' => 'clinic.buyer@bluezone.test',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $quotation = Quotation::create([
            'quotation_number' => 'QT-2026-8881',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'date' => now()->toDateString(),
            'valid_until' => now()->addDays(15)->toDateString(),
            'status' => 'Draft',
            'subtotal' => 300.00,
            'discount' => 0,
            'tax' => 45.00,
            'total' => 345.00,
            'created_by' => $this->adminUser->id,
        ]);

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'product_id' => $product->id,
            'item_type' => 'product',
            'item_name' => $product->name_en,
            'sku' => $product->sku,
            'quantity' => 3,
            'unit_price' => 100.00,
            'total' => 300.00,
        ]);

        $this->post(route('admin.sales.quotations.convert', $quotation))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $quotation->refresh();
        $this->assertEquals('Converted', $quotation->status);
        $this->assertNotNull($quotation->converted_order_id);

        $orderItem = OrderItem::where('order_id', $quotation->converted_order_id)->firstOrFail();
        $this->assertEquals($b1->id, $orderItem->product_batch_id);
        $this->assertEquals('LOT-QT-FEFO-1', $orderItem->batch_number);
        $this->assertEquals($earlierExp, substr((string) $orderItem->expiry_date, 0, 10));

        $b1->refresh();
        $this->assertEquals(2, $b1->available_qty);
        $this->assertEquals(2, $b1->current_qty);

        // Cancelling the order restores exact qty back to LOT-QT-FEFO-1 without creating duplicate batches
        $this->patch(route('admin.orders.update-status', $quotation->converted_order_id), [
            'status' => 'cancelled',
        ])->assertRedirect();

        $b1->refresh();
        $this->assertEquals(5, $b1->available_qty);
        $this->assertEquals(5, $b1->current_qty);
        $this->assertEquals(2, ProductBatch::where('product_id', $product->id)->count());
    }

    public function test_product_batch_ui_endpoints_and_expiry_cli_commands_and_valuation_report(): void
    {
        $this->actingAs($this->adminUser);
        $product = $this->createTestProduct('BZ-CLI-01', 12);

        InventoryService::syncAllProductsInventory();

        // Provisioned missing ProductBatch with default expiry date
        $autoBatch = ProductBatch::where('product_id', $product->id)->firstOrFail();
        $this->assertNotNull($autoBatch->expiry_date);

        // Update batch expiry via ProductBatchController
        $newExp = now()->addDays(45)->toDateString();
        $this->patchJson(route('admin.product-batches.update', $autoBatch), [
            'expiry_date' => $newExp,
            'status' => 'approved',
        ])->assertOk()->assertJsonPath('success', true);

        $this->assertEquals($newExp, $autoBatch->fresh()->expiry_date->toDateString());

        // Create an expired ProductBatch + an expired RawMaterialBatch and verify bz:check-expiries + invariant
        $expiredFg = ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => 'LOT-FG-EXPIRED-NOW',
            'location_id' => 'online',
            'initial_qty' => 4,
            'current_qty' => 4,
            'available_qty' => 4,
            'reserved_qty' => 0,
            'quarantine_qty' => 0,
            'manufacture_date' => now()->subYear()->toDateString(),
            'expiry_date' => now()->subDay()->toDateString(),
            'status' => 'approved',
        ]);

        $rm = RawMaterial::create([
            'code' => 'RM-EXP-01',
            'sku' => 'RM-EXP-01',
            'name_en' => 'Raw Test Compound',
            'name_ar' => 'مادة اختبارية',
            'category' => RawMaterialCategory::ActiveIngredient,
            'unit_of_measure' => UnitOfMeasure::Kilogram,
            'storage_condition' => StorageCondition::Ambient,
            'standard_cost' => 150.00,
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'code' => 'SUP-RM-01',
            'type' => SupplierType::IngredientSupplier,
            'name_en' => 'RM Supplier',
            'name_ar' => 'مورد خامات',
            'email' => 'rm.supplier@bluezone.test',
            'is_active' => true,
        ]);

        $rmBatch = RawMaterialBatch::create([
            'batch_number' => 'LOT-RM-EXPIRED-NOW',
            'supplier_lot' => 'SUP-LOT-EXP-01',
            'raw_material_id' => $rm->id,
            'supplier_id' => $supplier->id,
            'location_id' => 'central_wh',
            'initial_qty' => 5.0000,
            'current_qty' => 5.0000,
            'available_qty' => 5.0000,
            'reserved_qty' => 0.0000,
            'quarantine_qty' => 0.0000,
            'unit_cost' => 150.00,
            'manufacture_date' => now()->subYear()->toDateString(),
            'expiry_date' => now()->subDay()->toDateString(),
            'status' => RawMaterialBatchStatus::Approved,
        ]);

        $this->assertEquals(0, Artisan::call('bz:check-expiries'));
        $this->assertEquals(0, Artisan::call('bz:reconcile-batches'));

        $expiredFg->refresh();
        $rmBatch->refresh();
        $this->assertEquals('expired', $expiredFg->status);
        $this->assertEquals(0, $expiredFg->available_qty);
        $this->assertEquals(4, $expiredFg->quarantine_qty);
        $this->assertEquals(RawMaterialBatchStatus::Expired, $rmBatch->status);
        $this->assertEquals(0.0, (float) $rmBatch->available_qty);

        // Valuation & Expiry Report renders cleanly for both RM and FG without array/object crash
        $this->get(route('admin.supply-chain-reports.valuation-expiry'))
            ->assertOk()
            ->assertSee('LOT-FG-EXPIRED-NOW')
            ->assertSee('LOT-RM-EXPIRED-NOW');

        $this->get(route('admin.product-batches.index'))
            ->assertOk()
            ->assertSee('LOT-FG-EXPIRED-NOW');
    }

    public function test_invoice_show_print_and_update_compute_vat_after_discount_and_wrap_headers(): void
    {
        $this->actingAs($this->adminUser);
        $product = $this->createTestProduct('BZ-INV-MATH', 10);
        $exp = now()->addYear()->toDateString();

        $batch = ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => 'LOT-INV-MATH-01',
            'location_id' => 'online',
            'initial_qty' => 10,
            'current_qty' => 10,
            'available_qty' => 10,
            'manufacture_date' => now()->toDateString(),
            'expiry_date' => $exp,
            'status' => 'approved',
        ]);

        // Subtotal = 68, Discount = 20 => Net = 48.00, VAT(15%) = 7.20, Grand Total = 55.20
        $order = Order::create([
            'order_number' => 'ORD-MATH-001',
            'invoice_number' => 'INV-MATH-001',
            'channel' => 'online',
            'customer_name' => 'Math Verification Client',
            'customer_email' => 'math@bluezone.test',
            'date' => now()->toDateString(),
            'status' => 'delivered',
            'payment_method' => 'bank_transfer',
            'payment_status' => 'paid',
            'subtotal' => 68.00,
            'discount' => 20.00,
            'shipping' => 0.00,
            'tax' => 7.20,
            'total' => 55.20,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_batch_id' => $batch->id,
            'batch_number' => $batch->batch_number,
            'expiry_date' => $exp,
            'product_name_en' => $product->name_en,
            'product_name_ar' => $product->name_ar,
            'sku' => $product->sku,
            'unit_price' => 68.00,
            'quantity' => 1,
            'total' => 68.00,
        ]);

        $showResponse = $this->get(route('admin.invoices.show', $order->id));
        $showResponse->assertOk()
            ->assertSee('Exp: ' . $exp . ' / LOT-INV-MATH-01')
            ->assertSee('SAR 48.00')
            ->assertSee('SAR 7.20')
            ->assertSee('SAR 55.20');

        $printResponse = $this->get(route('admin.invoices.print', $order->id));
        $printResponse->assertOk()
            ->assertSee('Exp: ' . $exp . ' / LOT-INV-MATH-01')
            ->assertSee('SAR 48.00')
            ->assertSee('SAR 7.20')
            ->assertSee('SAR 55.20');

        // Updating invoice discount via studio endpoint recalculates VAT + total properly
        $this->postJson(route('admin.invoices.update', $order->id), [
            'discount' => 18.00, // Net = 50.00, VAT = 7.50, Total = 57.50
        ])->assertOk()->assertJsonPath('success', true);

        $order->refresh();
        $this->assertEquals(7.50, (float) $order->tax);
        $this->assertEquals(57.50, (float) $order->total);
    }
}
