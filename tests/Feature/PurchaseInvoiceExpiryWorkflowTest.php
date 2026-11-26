<?php

namespace Tests\Feature;

use App\Enums\PurchaseOrderStatus;
use App\Enums\RawMaterialBatchStatus;
use App\Models\Category;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Location;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\RawMaterial;
use App\Models\RawMaterialBatch;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseInvoiceExpiryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Supplier $supplier;
    protected RawMaterial $rawMaterial;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            ['name' => 'Super Admin', 'permissions' => ['*'], 'is_system' => true]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'finance-expiry@bluezone.test'],
            ['name' => 'Finance Admin', 'password' => bcrypt('secret'), 'role_id' => $role->id, 'status' => 'active']
        );

        Location::firstOrCreate(
            ['id' => 'central_wh'],
            ['name_en' => 'Central Warehouse', 'name_ar' => 'المستودع الرئيسي', 'type' => 'warehouse', 'is_active' => true]
        );

        $this->supplier = Supplier::create([
            'supplier_code' => 'SUP-TEST-01',
            'name_en' => 'Test Pharma Supplier',
            'name_ar' => 'مورد اختبار',
            'is_active' => true,
        ]);

        $this->rawMaterial = RawMaterial::create([
            'material_code' => 'RM-TEST-001',
            'name_en' => 'Sponge 6CM',
            'name_ar' => 'اسفنج 6 سم',
            'unit_of_measure' => 'pcs',
            'standard_cost' => 1.10,
            'shelf_life_days' => 730,
            'is_active' => true,
        ]);

        $category = Category::firstOrCreate(
            ['slug' => 'test-expiry-cat'],
            ['name_en' => 'Test Category', 'name_ar' => 'تصنيف اختبار']
        );

        $this->product = Product::create([
            'category_id' => $category->id,
            'sku' => 'FG-TEST-001',
            'slug' => 'fg-test-001',
            'name_en' => 'Finished Capsule',
            'name_ar' => 'كبسولة نهائية',
            'price' => 50,
            'cost_price' => 20,
            'stock_online' => 10,
            'is_active' => true,
        ]);
    }

    public function test_store_rejects_missing_or_past_expiry_date(): void
    {
        $responseMissing = $this->actingAs($this->admin)->post(route('admin.finance.purchase-invoices.store'), [
            'supplier_id' => $this->supplier->id,
            'invoice_date' => now()->toDateString(),
            'subtotal' => 110,
            'tax_amount' => 16.5,
            'grand_total' => 126.5,
            'items' => [
                [
                    'item_type' => 'raw_material',
                    'raw_material_id' => $this->rawMaterial->id,
                    'item_name' => 'Sponge 6CM',
                    'batch_number' => 'abc123',
                    'expiry_date' => '',
                    'quantity' => 100,
                    'unit_price' => 1.10,
                ],
            ],
        ]);

        $responseMissing->assertSessionHasErrors('items.0.expiry_date');

        $responsePast = $this->actingAs($this->admin)->post(route('admin.finance.purchase-invoices.store'), [
            'supplier_id' => $this->supplier->id,
            'invoice_date' => now()->toDateString(),
            'subtotal' => 110,
            'tax_amount' => 16.5,
            'grand_total' => 126.5,
            'items' => [
                [
                    'item_type' => 'raw_material',
                    'raw_material_id' => $this->rawMaterial->id,
                    'item_name' => 'Sponge 6CM',
                    'batch_number' => 'abc123',
                    'expiry_date' => now()->subDay()->toDateString(),
                    'quantity' => 100,
                    'unit_price' => 1.10,
                ],
            ],
        ]);

        $responsePast->assertSessionHasErrors('items.0.expiry_date');
    }

    public function test_store_saves_real_expiry_date_and_displays_both_batch_and_expiry_on_show_and_print(): void
    {
        $realExpiry = now()->addMonths(18)->toDateString();

        $response = $this->actingAs($this->admin)->post(route('admin.finance.purchase-invoices.store'), [
            'supplier_id' => $this->supplier->id,
            'invoice_date' => now()->toDateString(),
            'subtotal' => 310,
            'tax_amount' => 46.5,
            'grand_total' => 356.5,
            'items' => [
                [
                    'item_type' => 'raw_material',
                    'raw_material_id' => $this->rawMaterial->id,
                    'item_name' => 'Sponge 6CM',
                    'batch_number' => 'abc123',
                    'expiry_date' => $realExpiry,
                    'quantity' => 100,
                    'unit_price' => 1.10,
                ],
                [
                    'item_type' => 'product',
                    'product_id' => $this->product->id,
                    'item_name' => 'Finished Capsule',
                    'batch_number' => 'fg-lot-99',
                    'expiry_date' => $realExpiry,
                    'quantity' => 10,
                    'unit_price' => 20.00,
                ],
            ],
        ]);

        $invoice = PurchaseInvoice::latest('id')->firstOrFail();
        $response->assertRedirect(route('admin.finance.purchase-invoices.show', $invoice->id));

        $this->assertDatabaseHas('purchase_invoice_items', [
            'purchase_invoice_id' => $invoice->id,
            'batch_number' => 'abc123',
            'expiry_date' => $realExpiry . ' 00:00:00',
        ]);

        $this->assertDatabaseHas('product_batches', [
            'product_id' => $this->product->id,
            'batch_number' => 'fg-lot-99',
        ]);

        $showResponse = $this->actingAs($this->admin)->get(route('admin.finance.purchase-invoices.show', $invoice->id));
        $showResponse->assertOk();
        $showResponse->assertSee('Batch: abc123');
        $showResponse->assertSee('Exp: ' . $realExpiry);
        $showResponse->assertDontSee('LOT-2026 / 2028-12');

        $printResponse = $this->actingAs($this->admin)->get(route('admin.finance.purchase-invoices.print', $invoice->id));
        $printResponse->assertOk();
        $printResponse->assertSee('Batch: abc123');
        $printResponse->assertSee('Exp: ' . $realExpiry);
        $printResponse->assertDontSee('LOT-2026 / 2028-12');
    }

    public function test_update_item_batch_expiry_updates_invoice_item_and_synced_batch(): void
    {
        $batch = RawMaterialBatch::create([
            'raw_material_id' => $this->rawMaterial->id,
            'batch_number' => 'RM-SYNC-001',
            'supplier_lot' => 'OLD-LOT',
            'supplier_id' => $this->supplier->id,
            'location_id' => 'central_wh',
            'initial_qty' => 50,
            'current_qty' => 50,
            'available_qty' => 50,
            'reserved_qty' => 0,
            'quarantine_qty' => 0,
            'unit_cost' => 1.10,
            'manufacture_date' => now()->toDateString(),
            'expiry_date' => now()->addYear()->toDateString(),
            'status' => RawMaterialBatchStatus::Approved,
        ]);

        $invoice = PurchaseInvoice::create([
            'invoice_number' => 'PINV-2026-99999',
            'supplier_id' => $this->supplier->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => 55,
            'tax_amount' => 8.25,
            'grand_total' => 63.25,
            'paid_amount' => 0,
            'payment_status' => 'unpaid',
            'status' => 'approved',
        ]);

        $item = PurchaseInvoiceItem::create([
            'purchase_invoice_id' => $invoice->id,
            'item_type' => 'raw_material',
            'raw_material_id' => $this->rawMaterial->id,
            'raw_material_batch_id' => $batch->id,
            'batch_number' => 'OLD-LOT',
            'expiry_date' => null,
            'item_name' => 'Sponge 6CM',
            'quantity' => 50,
            'unit_price' => 1.10,
            'subtotal' => 55,
            'tax_rate' => 15,
            'tax_amount' => 8.25,
            'total' => 63.25,
        ]);

        $newExpiry = now()->addMonths(30)->toDateString();

        $response = $this->actingAs($this->admin)->patch(
            route('admin.finance.purchase-invoices.update-item-batch-expiry', [$invoice->id, $item->id]),
            [
                'batch_number' => 'REAL-LOT-2026',
                'expiry_date' => $newExpiry,
            ]
        );

        $response->assertRedirect();
        $this->assertEquals('REAL-LOT-2026', $item->fresh()->batch_number);
        $this->assertEquals($newExpiry, $item->fresh()->expiry_date->toDateString());
        $this->assertEquals('REAL-LOT-2026', $batch->fresh()->supplier_lot);
        $this->assertEquals($newExpiry, $batch->fresh()->expiry_date->toDateString());
    }

    public function test_grn_convert_to_bill_reuses_existing_batch_without_duplication(): void
    {
        $po = PurchaseOrder::create([
            'po_number' => 'PO-TEST-001',
            'supplier_id' => $this->supplier->id,
            'destination_location_id' => 'central_wh',
            'status' => PurchaseOrderStatus::Received,
            'order_date' => now()->toDateString(),
            'subtotal' => 110,
            'tax_amount' => 5.5,
            'grand_total' => 115.5,
        ]);

        $poItem = PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'item_type' => 'raw_material',
            'raw_material_id' => $this->rawMaterial->id,
            'item_name' => 'Sponge 6CM',
            'ordered_qty' => 100,
            'received_qty' => 100,
            'unit_price' => 1.10,
            'total_price' => 110,
        ]);

        $grn = GoodsReceipt::create([
            'grn_number' => 'GRN-TEST-001',
            'purchase_order_id' => $po->id,
            'supplier_id' => $this->supplier->id,
            'location_id' => 'central_wh',
            'receiver_id' => $this->admin->id,
            'received_date' => now()->toDateString(),
            'container_count' => 1,
            'status' => 'received',
        ]);

        $realExpiry = now()->addMonths(20)->toDateString();

        GoodsReceiptItem::create([
            'goods_receipt_id' => $grn->id,
            'purchase_order_item_id' => $poItem->id,
            'item_type' => 'raw_material',
            'raw_material_id' => $this->rawMaterial->id,
            'item_name' => 'Sponge 6CM',
            'ordered_qty' => 100,
            'received_qty' => 100,
            'supplier_lot' => 'SUP-LOT-GRN-1',
            'manufacture_date' => now()->subMonth()->toDateString(),
            'expiry_date' => $realExpiry,
        ]);

        $grnBatch = RawMaterialBatch::create([
            'raw_material_id' => $this->rawMaterial->id,
            'batch_number' => 'RM-GRN-001',
            'supplier_lot' => 'SUP-LOT-GRN-1',
            'purchase_order_id' => $po->id,
            'goods_receipt_id' => $grn->id,
            'supplier_id' => $this->supplier->id,
            'location_id' => 'central_wh',
            'initial_qty' => 100,
            'current_qty' => 100,
            'available_qty' => 0,
            'reserved_qty' => 0,
            'quarantine_qty' => 100,
            'unit_cost' => 1.10,
            'manufacture_date' => now()->subMonth()->toDateString(),
            'expiry_date' => $realExpiry,
            'status' => RawMaterialBatchStatus::Quarantine,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.goods-receipts.convert-to-bill', $grn));

        $invoice = PurchaseInvoice::latest('id')->firstOrFail();
        $response->assertRedirect(route('admin.finance.purchase-invoices.show', $invoice));

        $this->assertEquals(1, RawMaterialBatch::count());
        $this->assertDatabaseHas('purchase_invoice_items', [
            'purchase_invoice_id' => $invoice->id,
            'raw_material_batch_id' => $grnBatch->id,
            'batch_number' => 'SUP-LOT-GRN-1',
            'expiry_date' => $realExpiry . ' 00:00:00',
        ]);
    }
}
