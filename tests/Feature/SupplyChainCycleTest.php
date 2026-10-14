<?php

namespace Tests\Feature;

use App\Enums\PurchaseOrderStatus;
use App\Enums\RawMaterialBatchStatus;
use App\Enums\RawMaterialCategory;
use App\Enums\StorageCondition;
use App\Enums\SupplierType;
use App\Enums\UnitOfMeasure;
use App\Enums\WorkOrderStatus;
use App\Models\Category;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\ProductRecipeItem;
use App\Models\PurchaseOrder;
use App\Models\RawMaterial;
use App\Models\RawMaterialBatch;
use App\Models\RawMaterialMovement;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\RawMaterial\RawMaterialManager;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplyChainCycleTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $qaUser;

    protected User $procurementUser;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create([
            'name' => 'Super Administrator',
            'permissions' => ['*'],
        ]);

        $this->adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'status' => 'active',
        ]);

        $this->qaUser = User::factory()->create([
            'name' => 'Dr. Layla QA',
            'email' => 'qa.director@bluezone.ae',
            'role_id' => $adminRole->id,
            'status' => 'active',
        ]);

        $this->procurementUser = User::factory()->create([
            'name' => 'Tariq Procurement',
            'email' => 'procurement.head@bluezone.ae',
            'role_id' => $adminRole->id,
            'status' => 'active',
        ]);
    }

    public function test_supplier_dual_approval_requires_two_distinct_users(): void
    {
        $supplier = Supplier::create([
            'code' => 'SUP-TEST-001',
            'name_en' => 'BioPharma Test Lab',
            'name_ar' => 'مختبرات بايو فارما',
            'type' => SupplierType::IngredientSupplier,
            'country' => 'United States',
            'email' => 'compliance@biopharma.com',
            'lead_time_days' => 14,
            'is_active' => false,
        ]);

        $this->assertFalse($supplier->is_active);

        // 1. QA Director approves
        $supplier->approveByQa($this->qaUser);
        $this->assertFalse($supplier->fresh()->is_active);
        $this->assertNotNull($supplier->fresh()->qa_approved_at);

        // 2. Same QA user attempts to approve as Procurement Head -> must fail with DomainException
        $this->expectException(DomainException::class);
        $supplier->approveByProcurement($this->qaUser);
    }

    public function test_supplier_becomes_active_when_dual_signed_by_different_users(): void
    {
        $supplier = Supplier::create([
            'code' => 'SUP-TEST-002',
            'name_en' => 'Pure Bio Lab',
            'name_ar' => 'مختبرات بيور بايو',
            'type' => SupplierType::IngredientSupplier,
            'country' => 'Germany',
            'email' => 'pure@biolab.de',
            'lead_time_days' => 10,
            'is_active' => false,
        ]);

        $supplier->approveByQa($this->qaUser);
        $supplier->approveByProcurement($this->procurementUser);

        $fresh = $supplier->fresh();
        $this->assertTrue($fresh->is_active);
        $this->assertNotNull($fresh->qa_approved_at);
        $this->assertNotNull($fresh->procurement_approved_at);
    }

    public function test_purchase_order_atomic_number_generation_and_status_transitions(): void
    {
        $supplier = Supplier::create([
            'code' => 'SUP-TEST-003',
            'name_en' => 'Active Supplier',
            'name_ar' => 'مورد نشط',
            'type' => SupplierType::IngredientSupplier,
            'country' => 'UAE',
            'email' => 'info@supplier.ae',
            'is_active' => true,
        ]);

        $material = RawMaterial::create([
            'sku' => 'RM-TST-001',
            'name_en' => 'Test Material Purity 99%',
            'name_ar' => 'مادة تجريبية 99%',
            'category' => RawMaterialCategory::ActiveIngredient,
            'uom' => UnitOfMeasure::Kilogram,
            'storage_condition' => StorageCondition::Ambient,
            'reorder_level' => 10,
            'safety_stock' => 5,
        ]);

        $po = PurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'created_by' => $this->procurementUser->id,
            'order_date' => now(),
            'currency' => 'AED',
            'exchange_rate' => 1.0,
            'subtotal' => 30000,
            'total_amount' => 30000,
            'status' => PurchaseOrderStatus::Draft,
        ]);

        $this->assertMatchesRegularExpression('/^PO-\d{4}-\d{4}$/', $po->po_number);

        // Submit PO
        $po->transitionTo(PurchaseOrderStatus::Submitted);
        $this->assertEquals(PurchaseOrderStatus::Submitted, $po->status);

        // Separation of duties test: Above 25,000 AED, creator cannot self-approve
        $this->actingAs($this->procurementUser);
        $this->assertFalse($this->procurementUser->can('approve', $po));

        // Another authorized manager can approve
        $this->actingAs($this->adminUser);
        $this->assertTrue($this->adminUser->can('approve', $po));

        $po->transitionTo(PurchaseOrderStatus::Confirmed, ['approved_by' => $this->adminUser->id]);
        $this->assertEquals(PurchaseOrderStatus::Confirmed, $po->fresh()->status);
        $this->assertEquals($this->adminUser->id, $po->fresh()->approved_by);
    }

    public function test_inbound_grn_forces_quarantine_and_verifies_invariant(): void
    {
        $supplier = Supplier::create([
            'code' => 'SUP-GRN-01',
            'name_en' => 'Pharma Grade Co',
            'name_ar' => 'شركة فارما جريد السويسرية',
            'type' => SupplierType::IngredientSupplier,
            'country' => 'Switzerland',
            'email' => 'dock@pharmagrade.ch',
            'is_active' => true,
        ]);

        $material = RawMaterial::create([
            'sku' => 'RM-QNT-001',
            'name_en' => 'CoQ10 Ubiquinone Powder',
            'name_ar' => 'مسحوق يوبيكوينون CoQ10',
            'category' => RawMaterialCategory::ActiveIngredient,
            'uom' => UnitOfMeasure::Kilogram,
            'storage_condition' => StorageCondition::ColdRefrigerated,
            'reorder_level' => 20,
            'safety_stock' => 10,
        ]);

        $po = PurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'created_by' => $this->adminUser->id,
            'order_date' => now(),
            'currency' => 'AED',
            'exchange_rate' => 1.0,
            'subtotal' => 5000,
            'total_amount' => 5000,
            'status' => PurchaseOrderStatus::Confirmed,
        ]);

        $poItem = $po->items()->create([
            'raw_material_id' => $material->id,
            'quantity_ordered' => 50,
            'quantity_received' => 0,
            'unit_price' => 100,
            'total_price' => 5000,
        ]);

        $grn = GoodsReceipt::create([
            'purchase_order_id' => $po->id,
            'supplier_id' => $supplier->id,
            'received_by' => $this->adminUser->id,
            'received_date' => now(),
            'container_count' => 2,
            'temperature_reading' => 18.5,
            'seal_intact' => true,
        ]);

        $grnItem = $grn->items()->create([
            'po_item_id' => $poItem->id,
            'raw_material_id' => $material->id,
            'quantity_received' => 50,
            'supplier_lot' => 'SWISS-LOT-9988',
            'expiry_date' => now()->addMonths(24),
            'location_id' => 'central_wh',
        ]);

        // Receive batch via RawMaterialManager
        $manager = app(RawMaterialManager::class);
        $batch = $manager->receiveBatch($grnItem, $this->adminUser);

        // Assert Batch Status is strictly Quarantine
        $this->assertEquals(RawMaterialBatchStatus::Quarantine, $batch->status);
        $this->assertEquals(50.0000, (float) $batch->quarantine_qty);
        $this->assertEquals(0.0000, (float) $batch->available_qty);
        $this->assertEquals(50.0000, (float) $batch->current_qty);

        // Verify Invariant: current_qty = available_qty + reserved_qty + quarantine_qty
        $this->assertTrue($batch->verifyInvariant());

        // Assert Immutable Movement was recorded
        $movement = RawMaterialMovement::where('raw_material_batch_id', $batch->id)->first();
        $this->assertNotNull($movement);
        $this->assertEquals(50.0000, (float) $movement->quantity);

        // Immutability Guard Test: Attempting to modify movement throws DomainException
        $this->expectException(DomainException::class);
        $movement->update(['notes' => 'Tampered notes']);
    }

    public function test_fefo_dispense_prioritizes_earliest_expiry_among_approved_batches(): void
    {
        $material = RawMaterial::create([
            'sku' => 'RM-FEFO-001',
            'name_en' => 'Resveratrol Micronized 98%',
            'name_ar' => 'ريزفيراترول نقي 98%',
            'category' => RawMaterialCategory::ActiveIngredient,
            'uom' => UnitOfMeasure::Kilogram,
            'storage_condition' => StorageCondition::Ambient,
            'reorder_level' => 10,
            'safety_stock' => 5,
        ]);

        $supplier = Supplier::create([
            'code' => 'SUP-FEFO-01',
            'name_en' => 'Active Herbals Lab',
            'name_ar' => 'مختبرات الأعشاب النشطة',
            'type' => SupplierType::IngredientSupplier,
            'country' => 'United States',
            'email' => 'herbal@active.com',
            'is_active' => true,
        ]);

        // Batch 1: Expiring in 18 months
        $batchFar = RawMaterialBatch::create([
            'batch_number' => 'LOT-RM-2026-FAR',
            'supplier_id' => $supplier->id,
            'supplier_lot' => 'VEND-LOT-FAR',
            'raw_material_id' => $material->id,
            'location_id' => 'central_wh',
            'current_qty' => 100,
            'available_qty' => 100,
            'quarantine_qty' => 0,
            'reserved_qty' => 0,
            'status' => RawMaterialBatchStatus::Approved,
            'expiry_date' => now()->addMonths(18),
        ]);

        // Batch 2: Expiring in 3 months (Near)
        $batchNear = RawMaterialBatch::create([
            'batch_number' => 'LOT-RM-2026-NEAR',
            'supplier_id' => $supplier->id,
            'supplier_lot' => 'VEND-LOT-NEAR',
            'raw_material_id' => $material->id,
            'location_id' => 'central_wh',
            'current_qty' => 100,
            'available_qty' => 100,
            'quarantine_qty' => 0,
            'reserved_qty' => 0,
            'status' => RawMaterialBatchStatus::Approved,
            'expiry_date' => now()->addMonths(3),
        ]);

        $category = Category::create([
            'name_en' => 'Longevity Supplements',
            'name_ar' => 'مكملات إطالة العمر',
            'slug' => 'longevity-supplements',
        ]);

        $product = Product::create([
            'name_en' => 'Cellular Shield 500mg',
            'name_ar' => 'درع الخلايا 500 ملغ',
            'slug' => 'cellular-shield-500mg',
            'sku' => 'BZ-CELL-01',
            'category_id' => $category->id,
            'price' => 250.00,
        ]);

        ProductRecipeItem::create([
            'product_id' => $product->id,
            'raw_material_id' => $material->id,
            'quantity' => 0.05, // 50g per unit
            'loss_percentage' => 0.00,
        ]);

        $wo = WorkOrder::create([
            'product_id' => $product->id,
            'product_batch_number' => 'BZ-PROD-2026-001',
            'target_quantity' => 1000, // needs 50kg
            'scheduled_date' => now(),
            'status' => WorkOrderStatus::Scheduled,
            'created_by' => $this->adminUser->id,
        ]);

        $manager = app(RawMaterialManager::class);

        // FEFO Pick & Reservation
        $allocated = $manager->reserveForWorkOrder($wo, $this->adminUser);

        // Must allocate strictly from $batchNear first
        $this->assertEquals($batchNear->id, $allocated[0]['batch']->id);
        $this->assertEquals(50.0000, (float) $batchNear->fresh()->reserved_qty);
        $this->assertEquals(50.0000, (float) $batchNear->fresh()->available_qty);
        $this->assertEquals(0.0000, (float) $batchFar->fresh()->reserved_qty);

        // Start WO and Dispense
        $wo->update(['status' => WorkOrderStatus::Dispensing]);
        $consumption = $manager->dispenseMaterial(
            $wo,
            $batchNear,
            50.0000,
            $this->adminUser,
            'VERIFIED-BC-12345'
        );

        $this->assertEquals(50.0000, (float) $consumption->quantity);
        $this->assertEquals(50.0000, (float) $batchNear->fresh()->current_qty);
        $this->assertEquals(0.0000, (float) $batchNear->fresh()->reserved_qty);

        // Test consumption immutability guard
        $this->expectException(DomainException::class);
        $consumption->update(['quantity' => 10.0000]);
    }

    public function test_recall_traceability_resolves_pedigree_rapidly(): void
    {
        $material = RawMaterial::create([
            'sku' => 'RM-RECALL-01',
            'name_en' => 'Recall Test Material',
            'name_ar' => 'مادة اختبار التتبع والاسترجاع',
            'category' => RawMaterialCategory::ActiveIngredient,
            'uom' => UnitOfMeasure::Kilogram,
            'storage_condition' => StorageCondition::Ambient,
            'reorder_level' => 10,
            'safety_stock' => 5,
        ]);

        $supplier = Supplier::create([
            'code' => 'SUP-REC-01',
            'name_en' => 'Traceability Supplier Inc',
            'name_ar' => 'شركة التتبع السريع للتوريد',
            'type' => SupplierType::IngredientSupplier,
            'country' => 'United Kingdom',
            'email' => 'recall@traceability.co.uk',
            'is_active' => true,
        ]);

        $batch = RawMaterialBatch::create([
            'batch_number' => 'LOT-RM-202609-0001',
            'supplier_id' => $supplier->id,
            'supplier_lot' => 'VENDOR-RECALL-LOT-01',
            'raw_material_id' => $material->id,
            'location_id' => 'central_wh',
            'current_qty' => 100,
            'available_qty' => 100,
            'quarantine_qty' => 0,
            'reserved_qty' => 0,
            'status' => RawMaterialBatchStatus::Approved,
            'expiry_date' => now()->addMonths(12),
        ]);

        $startTime = microtime(true);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.supply-chain-reports.lot-traceability', [
                'direction' => 'forward',
                'lot_number' => 'LOT-RM-202609-0001',
            ]));

        $duration = microtime(true) - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(10.0, $duration, 'Genealogy trace query executed rapidly.');
    }

    public function test_translation_key_parity_between_en_and_ar(): void
    {
        $files = [
            'suppliers',
            'raw_materials',
            'purchase_orders',
            'goods_receipts',
            'batches',
            'qc',
            'production',
            'supply_chain_reports',
        ];

        foreach ($files as $file) {
            $enPath = lang_path("en/{$file}.php");
            $arPath = lang_path("ar/{$file}.php");

            $this->assertFileExists($enPath);
            $this->assertFileExists($arPath);

            $en = require $enPath;
            $ar = require $arPath;

            $this->assertEquals(
                array_keys($en),
                array_keys($ar),
                "Translation keys between en/{$file}.php and ar/{$file}.php must have 100% parity."
            );
        }
    }

    public function test_raw_materials_index_route_renders_successfully(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.raw-materials.index'));

        $response->assertStatus(200);
        $response->assertSee(__('raw_materials.title'));
    }

    public function test_all_supply_chain_index_and_report_routes_render_cleanly(): void
    {
        $routes = [
            'admin.suppliers.index',
            'admin.raw-materials.index',
            'admin.purchase-orders.index',
            'admin.goods-receipts.index',
            'admin.batches.index',
            'admin.qc.index',
            'admin.work-orders.index',
            'admin.supply-chain-reports.vendor-scorecard',
            'admin.supply-chain-reports.valuation-expiry',
            'admin.supply-chain-reports.po-aging',
            'admin.supply-chain-reports.qc-log',
            'admin.supply-chain-reports.mrp-forecast',
            'admin.supply-chain-reports.scrap-variance',
            'admin.supply-chain-reports.lot-traceability',
        ];

        foreach ($routes as $routeName) {
            $response = $this->actingAs($this->adminUser)->get(route($routeName));
            if ($response->status() !== 200 && $response->exception) {
                dump("Error on [{$routeName}]: ".$response->exception->getMessage().' at '.$response->exception->getFile().':'.$response->exception->getLine());
            }
            $this->assertEquals(
                200,
                $response->status(),
                "Route [{$routeName}] must return HTTP 200 without any runtime or view errors."
            );
        }
    }

    public function test_roles_matrix_includes_supply_chain_domain(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.roles.matrix'));
        $response->assertStatus(200);
        $response->assertSee('Supply Chain');
        $response->assertSee('Suppliers & Approved Vendors');
        $response->assertSee('Raw Materials & Active APIs Catalog');
        $response->assertSee('Purchase Orders & Procurement');
    }

    public function test_qa_role_access_control(): void
    {
        $qaRole = Role::create([
            'name' => 'QA Specialist',
            'permissions' => ['qc.*', 'batches.*', 'raw_materials.*', 'supply_chain_reports.*'],
        ]);

        $qaUser = User::factory()->create([
            'role_id' => $qaRole->id,
            'status' => 'active',
        ]);

        // Authorized routes
        $this->actingAs($qaUser)->get(route('admin.qc.index'))->assertStatus(200);
        $this->actingAs($qaUser)->get(route('admin.batches.index'))->assertStatus(200);
        $this->actingAs($qaUser)->get(route('admin.raw-materials.index'))->assertStatus(200);

        // Forbidden routes (Purchase orders / Work orders)
        $this->actingAs($qaUser)->get(route('admin.purchase-orders.index'))->assertStatus(403);
        $this->actingAs($qaUser)->get(route('admin.work-orders.index'))->assertStatus(403);
    }

    public function test_procurement_role_access_control(): void
    {
        $procRole = Role::create([
            'name' => 'Procurement Officer',
            'permissions' => ['suppliers.*', 'purchase_orders.*', 'goods_receipts.*', 'raw_materials.view'],
        ]);

        $procUser = User::factory()->create([
            'role_id' => $procRole->id,
            'status' => 'active',
        ]);

        // Authorized routes
        $this->actingAs($procUser)->get(route('admin.suppliers.index'))->assertStatus(200);
        $this->actingAs($procUser)->get(route('admin.purchase-orders.index'))->assertStatus(200);
        $this->actingAs($procUser)->get(route('admin.goods-receipts.index'))->assertStatus(200);
        $this->actingAs($procUser)->get(route('admin.raw-materials.index'))->assertStatus(200);

        // Forbidden routes (QC / Work Orders)
        $this->actingAs($procUser)->get(route('admin.qc.index'))->assertStatus(403);
        $this->actingAs($procUser)->get(route('admin.work-orders.index'))->assertStatus(403);
    }
}
