<?php

namespace Tests\Feature;

use App\Enums\GoodsReceiptStatus;
use App\Enums\PurchaseOrderStatus;
use App\Models\GoodsReceipt;
use App\Models\Location;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\RawMaterial;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoodsReceiptModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Supplier $supplier;
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
    }

    public function test_can_view_goods_receipts_index_and_show(): void
    {
        $po = PurchaseOrder::create([
            'supplier_id' => $this->supplier->id,
            'status' => PurchaseOrderStatus::Confirmed,
            'order_date' => now()->toDateString(),
            'destination_location_id' => $this->location->id,
        ]);

        $grn = GoodsReceipt::create([
            'purchase_order_id' => $po->id,
            'supplier_id' => $this->supplier->id,
            'location_id' => $this->location->id,
            'received_date' => now()->toDateString(),
            'receiver_id' => $this->admin->id,
            'container_count' => 2,
            'status' => GoodsReceiptStatus::Received,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.goods-receipts.index'))
            ->assertStatus(200);

        $this->actingAs($this->admin)
            ->get(route('admin.goods-receipts.show', $grn))
            ->assertStatus(200)
            ->assertSee($grn->grn_number);
    }

    public function test_can_access_edit_screen_for_goods_receipt(): void
    {
        $po = PurchaseOrder::create([
            'supplier_id' => $this->supplier->id,
            'status' => PurchaseOrderStatus::Confirmed,
            'order_date' => now()->toDateString(),
            'destination_location_id' => $this->location->id,
        ]);

        $grn = GoodsReceipt::create([
            'purchase_order_id' => $po->id,
            'supplier_id' => $this->supplier->id,
            'location_id' => $this->location->id,
            'received_date' => now()->toDateString(),
            'receiver_id' => $this->admin->id,
            'container_count' => 2,
            'status' => GoodsReceiptStatus::Received,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.goods-receipts.edit', $grn))
            ->assertStatus(200)
            ->assertSee($grn->grn_number);
    }
}
