<?php

namespace Tests\Feature;

use App\Enums\GoodsReceiptStatus;
use App\Enums\PurchaseOrderStatus;
use App\Models\GoodsReceipt;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Location;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\RawMaterial;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchasesModuleIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Supplier $supplier;
    protected Product $product;
    protected RawMaterial $rawMaterial;
    protected Location $location;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create([
            'name' => 'Super Administrator',
            'permissions' => ['*'],
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'status' => 'active',
        ]);

        $this->supplier = Supplier::create([
            'name_en' => 'Longevity Labs Inc',
            'name_ar' => 'لونجيفيتي لابس',
            'code' => 'VND-LONGEVITY',
            'type' => 'ingredient_supplier',
            'email' => 'sales@longevitylabs.com',
            'currency' => 'AED',
            'is_active' => true,
        ]);

        $this->location = Location::firstOrCreate(
            ['id' => 'central_wh'],
            ['name_en' => 'Central Warehouse', 'name_ar' => 'المستودع المركزي', 'is_active' => true]
        );

        $this->product = Product::create([
            'name_en' => 'NMN Youth Booster 500mg',
            'name_ar' => 'معزز الشباب NMN 500 ملغ',
            'slug' => 'nmn-youth-booster-500mg',
            'sku' => 'NMN-500',
            'price' => 120.00,
            'cost_price' => 45.00,
            'stock_online' => 10,
            'stock_offline' => 20,
            'status' => 'active',
        ]);
    }

    public function test_can_create_purchase_order_with_commercial_product(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.purchase-orders.store'), [
            'supplier_id' => $this->supplier->id,
            'order_date' => now()->toDateString(),
            'expected_date' => now()->addDays(7)->toDateString(),
            'destination_location_id' => $this->location->id,
            'currency' => 'AED',
            'exchange_rate' => 1.0000,
            'freight_cost' => 50.00,
            'customs_duties' => 20.00,
            'tax_amount' => 10.00,
            'supplier_reference' => 'QUOTE-2026-NMN',
            'items' => [
                [
                    'item_type' => 'product',
                    'product_id' => $this->product->id,
                    'ordered_qty' => 100,
                    'unit_price' => 45.00,
                    'notes' => 'Bulk commercial pack',
                ],
            ],
        ]);

        $po = PurchaseOrder::where('supplier_id', $this->supplier->id)->first();
        $this->assertNotNull($po);
        $this->assertEquals(PurchaseOrderStatus::Draft, $po->status);
        $this->assertEquals(4500.00, (float) $po->subtotal);
        $this->assertEquals(4580.00, (float) $po->grand_total);

        $item = $po->items->first();
        $this->assertNotNull($item);
        $this->assertEquals('product', $item->item_type);
        $this->assertEquals($this->product->id, $item->product_id);
        $this->assertEquals(100, (float) $item->ordered_qty);
        $this->assertEquals('NMN Youth Booster 500mg', $item->item_name);
        $this->assertEquals('NMN-500', $item->item_sku);

        $response->assertRedirect(route('admin.purchase-orders.show', $po));
    }

    public function test_can_receive_commercial_product_and_credit_warehouse_inventory(): void
    {
        // 1. Create and confirm PO
        $po = PurchaseOrder::create([
            'po_number' => PurchaseOrder::generatePoNumber(),
            'supplier_id' => $this->supplier->id,
            'destination_location_id' => $this->location->id,
            'order_date' => now()->toDateString(),
            'status' => PurchaseOrderStatus::Confirmed,
            'currency' => 'AED',
            'exchange_rate' => 1.0000,
            'subtotal' => 4500.00,
            'grand_total' => 4500.00,
            'created_by' => $this->admin->id,
        ]);

        $poItem = $po->items()->create([
            'item_type' => 'product',
            'product_id' => $this->product->id,
            'ordered_qty' => 100,
            'received_qty' => 0,
            'unit_price' => 45.00,
            'total_price' => 4500.00,
        ]);

        // 2. Post Goods Receipt
        $response = $this->actingAs($this->admin)->post(route('admin.goods-receipts.store'), [
            'purchase_order_id' => $po->id,
            'location_id' => $this->location->id,
            'received_date' => now()->toDateString(),
            'carrier_name' => 'DHL Express',
            'tracking_number' => 'AWB-883921',
            'container_count' => 4,
            'seal_verified' => true,
            'temperature_logger_reading' => 20.5,
            'temperature_within_range' => true,
            'items' => [
                [
                    'purchase_order_item_id' => $poItem->id,
                    'item_type' => 'product',
                    'product_id' => $this->product->id,
                    'received_qty' => 100,
                    'supplier_lot' => 'LOT-NMN-2026-01',
                    'manufacture_date' => now()->subMonth()->toDateString(),
                    'expiry_date' => now()->addYears(2)->toDateString(),
                    'storage_bin' => 'BIN-SHELF-B1',
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $grn = GoodsReceipt::where('purchase_order_id', $po->id)->first();
        $this->assertNotNull($grn);
        $this->assertEquals(GoodsReceiptStatus::Received, $grn->status);

        // Check PO status progressed to Received
        $po->refresh();
        $this->assertEquals(PurchaseOrderStatus::Received, $po->status);

        // Check Inventory item was updated
        $invItem = InventoryItem::where('product_id', $this->product->id)
            ->where('location_id', $this->location->id)
            ->first();
        $this->assertNotNull($invItem);
        $this->assertGreaterThanOrEqual(100, $invItem->current_stock);

        // Check Inventory movement was logged
        $movement = InventoryMovement::where('product_id', $this->product->id)
            ->where('movement_type', 'Purchase Receipt')
            ->first();
        $this->assertNotNull($movement);
        $this->assertEquals(100, $movement->quantity);
        $this->assertStringContainsString($grn->grn_number, $movement->note);

        $response->assertRedirect(route('admin.goods-receipts.show', $grn));
    }

    public function test_can_generate_3_way_match_bill_from_goods_receipt(): void
    {
        // 1. Create PO & GRN
        $po = PurchaseOrder::create([
            'po_number' => PurchaseOrder::generatePoNumber(),
            'supplier_id' => $this->supplier->id,
            'destination_location_id' => $this->location->id,
            'order_date' => now()->toDateString(),
            'status' => PurchaseOrderStatus::Received,
            'currency' => 'AED',
            'exchange_rate' => 1.0000,
            'subtotal' => 4500.00,
            'grand_total' => 4500.00,
            'created_by' => $this->admin->id,
        ]);

        $poItem = $po->items()->create([
            'item_type' => 'product',
            'product_id' => $this->product->id,
            'ordered_qty' => 100,
            'received_qty' => 100,
            'unit_price' => 45.00,
            'total_price' => 4500.00,
        ]);

        $grn = GoodsReceipt::create([
            'grn_number' => GoodsReceipt::generateGrnNumber(),
            'purchase_order_id' => $po->id,
            'supplier_id' => $this->supplier->id,
            'location_id' => $this->location->id,
            'receiver_id' => $this->admin->id,
            'received_date' => now()->toDateString(),
            'container_count' => 2,
            'seal_verified' => true,
            'status' => GoodsReceiptStatus::Received,
        ]);

        $grn->items()->create([
            'purchase_order_item_id' => $poItem->id,
            'item_type' => 'product',
            'product_id' => $this->product->id,
            'ordered_qty' => 100,
            'received_qty' => 100,
            'rejected_qty' => 0,
            'supplier_lot' => 'LOT-MATCH-99',
            'manufacture_date' => now()->subMonth()->toDateString(),
            'expiry_date' => now()->addYears(2)->toDateString(),
        ]);

        // 2. Trigger 3-Way Match Bill Generation
        $response = $this->actingAs($this->admin)->post(route('admin.goods-receipts.generate-bill', $grn));

        $invoice = PurchaseInvoice::where('purchase_order_id', $po->id)
            ->where('reference_number', $grn->grn_number)
            ->first();

        $this->assertNotNull($invoice);
        $this->assertEquals($this->supplier->id, $invoice->supplier_id);
        $this->assertEquals(4500.00, (float) $invoice->subtotal);
        $this->assertEquals(225.00, (float) $invoice->tax_amount); // 5% VAT
        $this->assertEquals(4725.00, (float) $invoice->grand_total);
        $this->assertEquals('approved', $invoice->status);

        $response->assertRedirect(route('admin.finance.purchase-invoices.show', $invoice));
    }
}
