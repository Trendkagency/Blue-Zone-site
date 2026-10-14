<?php

namespace Database\Seeders;

use App\Enums\PurchaseOrderStatus;
use App\Enums\QcVerdict;
use App\Enums\RawMaterialBatchStatus;
use App\Enums\RawMaterialCategory;
use App\Enums\StorageCondition;
use App\Enums\SupplierType;
use App\Enums\UnitOfMeasure;
use App\Enums\WorkOrderStatus;
use App\Models\Country;
use App\Models\GoodsReceipt;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductRecipeItem;
use App\Models\PurchaseOrder;
use App\Models\QcInspection;
use App\Models\RawMaterial;
use App\Models\RawMaterialBatch;
use App\Models\Supplier;
use App\Models\SupplierMaterialPrice;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderMaterialConsumption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplyChainSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get or create testing users
        $admin = User::first() ?? User::factory()->create([
            'name' => 'Dr. Zaid Al-Nuaimi',
            'email' => 'zaid@bluezone.com',
            'status' => 'active',
        ]);

        $qaDirector = User::where('email', 'qa@bluezone.com')->first() ?? User::factory()->create([
            'name' => 'Dr. Sarah Al-Mansouri (QA Director)',
            'email' => 'qa@bluezone.com',
            'status' => 'active',
        ]);

        $procurementHead = User::where('email', 'procurement@bluezone.com')->first() ?? User::factory()->create([
            'name' => 'Eng. Tariq Al-Hashimi (Procurement)',
            'email' => 'procurement@bluezone.com',
            'status' => 'active',
        ]);

        $chemist = User::where('email', 'chemist@bluezone.com')->first() ?? User::factory()->create([
            'name' => 'Analyst Karim Mahmoud (Chemist)',
            'email' => 'chemist@bluezone.com',
            'status' => 'active',
        ]);

        $country = Country::first() ?? Country::create([
            'code' => 'AE',
            'name_en' => 'United Arab Emirates',
            'name_ar' => 'الإمارات العربية المتحدة',
            'phone_code' => '+971',
            'currency_code' => 'AED',
            'is_active' => true,
        ]);

        $location = Location::where('id', 'central_wh')->first() ?? Location::create([
            'id' => 'central_wh',
            'name_en' => 'Central Logistics Warehouse',
            'name_ar' => 'المستودع اللوجستي المركزي',
            'code' => 'LOC-CENTRAL',
            'type' => 'warehouse',
            'is_active' => true,
        ]);

        // 2. Seed Suppliers
        $chromadex = Supplier::create([
            'code' => 'SUP-CHROMA-01',
            'type' => SupplierType::IngredientSupplier,
            'name_en' => 'ChromaDex Bioceuticals Inc.',
            'name_ar' => 'شركة كروما دكس للمواد الحيوية',
            'contact_name' => 'Dr. Frank Jaksch',
            'email' => 'sourcing@chromadex.com',
            'phone' => '+13105550192',
            'country_id' => $country->id,
            'city' => 'Los Angeles',
            'address' => '10900 Wilshire Blvd, Suite 600',
            'rating' => 4.95,
            'gmp_certified' => true,
            'iso_certified' => true,
            'audit_expiry' => now()->addMonths(14)->toDateString(),
            'payment_terms' => 'NET30',
            'currency' => 'AED',
            'lead_time_days' => 12,
            'is_active' => true,
            'qa_approved_by' => $qaDirector->id,
            'qa_approved_at' => now()->subMonths(3),
            'procurement_approved_by' => $procurementHead->id,
            'procurement_approved_at' => now()->subMonths(3),
        ]);

        $biovita = Supplier::create([
            'code' => 'SUP-BIOVIT-02',
            'type' => SupplierType::IngredientSupplier,
            'name_en' => 'Biovita Nutriceuticals GmbH',
            'name_ar' => 'بيوفيتا نوتريسوتيكالز الألمانية',
            'contact_name' => 'Klaus Weber',
            'email' => 'sales@biovita-de.com',
            'phone' => '+49892030040',
            'country_id' => $country->id,
            'city' => 'Munich',
            'address' => 'Bavariaring 14, 80336 München',
            'rating' => 4.80,
            'gmp_certified' => true,
            'iso_certified' => true,
            'audit_expiry' => now()->addMonths(8)->toDateString(),
            'payment_terms' => 'NET30',
            'currency' => 'AED',
            'lead_time_days' => 16,
            'is_active' => true,
            'qa_approved_by' => $qaDirector->id,
            'qa_approved_at' => now()->subMonths(2),
            'procurement_approved_by' => $procurementHead->id,
            'procurement_approved_at' => now()->subMonths(2),
        ]);

        $eastpack = Supplier::create([
            'code' => 'SUP-EASTPK-03',
            'type' => SupplierType::PackagingVendor,
            'name_en' => 'EastPack Amber Glass Co.',
            'name_ar' => 'شركة إيست باك للزجاج والعبوات الدوائية',
            'contact_name' => 'Li Wei',
            'email' => 'export@eastpack-glass.com',
            'phone' => '+862158880199',
            'country_id' => $country->id,
            'city' => 'Shanghai',
            'address' => 'Pudong New District, Industry Zone B',
            'rating' => 4.15,
            'gmp_certified' => false,
            'iso_certified' => true,
            'audit_expiry' => now()->addMonths(5)->toDateString(),
            'payment_terms' => 'NET60',
            'currency' => 'AED',
            'lead_time_days' => 28,
            'is_active' => true,
            'qa_approved_by' => $qaDirector->id,
            'qa_approved_at' => now()->subMonths(1),
            'procurement_approved_by' => $procurementHead->id,
            'procurement_approved_at' => now()->subMonths(1),
        ]);

        $pendingSupplier = Supplier::create([
            'code' => 'SUP-NEWLAB-04',
            'type' => SupplierType::IngredientSupplier,
            'name_en' => 'Sourcing Global Labs Ltd.',
            'name_ar' => 'مختبرات التوريد العالمية',
            'contact_name' => 'Rajesh Sharma',
            'email' => 'contact@sourcing-global.com',
            'phone' => '+912267890123',
            'country_id' => $country->id,
            'city' => 'Mumbai',
            'address' => 'Bandra Kurla Complex',
            'rating' => 3.80,
            'gmp_certified' => true,
            'iso_certified' => false,
            'audit_expiry' => now()->addMonths(2)->toDateString(),
            'payment_terms' => 'Advance',
            'currency' => 'AED',
            'lead_time_days' => 20,
            'is_active' => false, // Under review (only 1 approval)
            'qa_approved_by' => $qaDirector->id,
            'qa_approved_at' => now(),
            'procurement_approved_by' => null,
            'procurement_approved_at' => null,
        ]);

        // 3. Seed Raw Materials
        $nmn = RawMaterial::create([
            'code' => 'RM-NMN-BIO',
            'name_en' => 'Beta-Nicotinamide Mononucleotide (NMN 99.8%)',
            'name_ar' => 'بيتا-نيكوتيناميد أحادي النوكليوتيد (NMN 99.8%)',
            'inci_name' => 'Nicotinamide Mononucleotide',
            'cas_number' => '1094-61-7',
            'category' => RawMaterialCategory::ActiveIngredient,
            'unit_of_measure' => UnitOfMeasure::Kilogram,
            'storage_condition' => StorageCondition::ColdRefrigerated,
            'standard_cost' => 8500.0000,
            'min_reorder_qty' => 10.0000,
            'safety_stock' => 5.0000,
            'shelf_life_days' => 730,
            'requires_coa' => true,
            'default_supplier_id' => $chromadex->id,
            'is_active' => true,
        ]);

        $resveratrol = RawMaterial::create([
            'code' => 'RM-RESV-99',
            'name_en' => 'Trans-Resveratrol 99% Pure Micronized',
            'name_ar' => 'ترانس-ريزفيراترول نقي 99% ميكروني',
            'inci_name' => 'Resveratrol',
            'cas_number' => '501-36-0',
            'category' => RawMaterialCategory::ActiveIngredient,
            'unit_of_measure' => UnitOfMeasure::Kilogram,
            'storage_condition' => StorageCondition::ColdRefrigerated,
            'standard_cost' => 4200.0000,
            'min_reorder_qty' => 15.0000,
            'safety_stock' => 5.0000,
            'shelf_life_days' => 730,
            'requires_coa' => true,
            'default_supplier_id' => $biovita->id,
            'is_active' => true,
        ]);

        $capsules = RawMaterial::create([
            'code' => 'RM-CAPS-00',
            'name_en' => 'Plant Cellulose Vegan Capsules Size 00',
            'name_ar' => 'كبسولات نباتية سليلوزية قياس 00',
            'inci_name' => 'Hypromellose (HPMC)',
            'cas_number' => '9004-65-3',
            'category' => RawMaterialCategory::PrimaryPackaging,
            'unit_of_measure' => UnitOfMeasure::Pieces,
            'storage_condition' => StorageCondition::Ambient,
            'standard_cost' => 0.0800,
            'min_reorder_qty' => 50000.0000,
            'safety_stock' => 20000.0000,
            'shelf_life_days' => 1825,
            'requires_coa' => false,
            'default_supplier_id' => $eastpack->id,
            'is_active' => true,
        ]);

        $bottles = RawMaterial::create([
            'code' => 'RM-BOTT-60',
            'name_en' => 'Pharmaceutical Amber Glass Bottle 60ml + Childproof Cap',
            'name_ar' => 'عبوة زجاجية كهرمانية صيدلانية 60 مل مع غطاء آمن',
            'inci_name' => 'Type III Amber Glass Container',
            'cas_number' => null,
            'category' => RawMaterialCategory::PrimaryPackaging,
            'unit_of_measure' => UnitOfMeasure::Pieces,
            'storage_condition' => StorageCondition::Ambient,
            'standard_cost' => 1.8500,
            'min_reorder_qty' => 5000.0000,
            'safety_stock' => 2000.0000,
            'shelf_life_days' => 3650,
            'requires_coa' => false,
            'default_supplier_id' => $eastpack->id,
            'is_active' => true,
        ]);

        // 4. Seed Supplier Material Prices
        SupplierMaterialPrice::create([
            'supplier_id' => $chromadex->id,
            'raw_material_id' => $nmn->id,
            'unit_price' => 8400.0000,
            'currency' => 'AED',
            'min_order_qty' => 5.0000,
            'lead_time_days' => 12,
            'is_preferred' => true,
        ]);

        SupplierMaterialPrice::create([
            'supplier_id' => $biovita->id,
            'raw_material_id' => $resveratrol->id,
            'unit_price' => 4150.0000,
            'currency' => 'AED',
            'min_order_qty' => 10.0000,
            'lead_time_days' => 16,
            'is_preferred' => true,
        ]);

        // 5. Seed Purchase Orders & Goods Receipts
        $po1 = PurchaseOrder::create([
            'po_number' => 'PO-2026-0001',
            'supplier_id' => $chromadex->id,
            'order_date' => now()->subDays(45)->toDateString(),
            'expected_date' => now()->subDays(30)->toDateString(),
            'destination_location_id' => $location->id,
            'status' => PurchaseOrderStatus::Received,
            'subtotal' => 168000.00,
            'freight_cost' => 2500.00,
            'customs_duties' => 4500.00,
            'tax_amount' => 8400.00,
            'grand_total' => 183400.00,
            'currency' => 'AED',
            'created_by' => $procurementHead->id,
            'approved_by' => $admin->id,
            'approved_at' => now()->subDays(44),
        ]);

        $po1Item = $po1->items()->create([
            'raw_material_id' => $nmn->id,
            'ordered_qty' => 20.0000,
            'received_qty' => 20.0000,
            'unit_price' => 8400.0000,
            'total_price' => 168000.00,
        ]);

        $grn1 = GoodsReceipt::create([
            'grn_number' => 'GRN-2026-0001',
            'purchase_order_id' => $po1->id,
            'supplier_id' => $chromadex->id,
            'location_id' => $location->id,
            'received_date' => now()->subDays(32)->toDateString(),
            'receiver_id' => $admin->id,
            'carrier_name' => 'DHL Cold-Chain Express',
            'tracking_number' => 'DHL-CC-994821',
            'temperature_logger_reading' => 4.20,
            'temperature_within_range' => true,
            'seal_verified' => true,
            'container_count' => 2,
            'status' => 'received',
        ]);

        $grn1Item = $grn1->items()->create([
            'purchase_order_item_id' => $po1Item->id,
            'raw_material_id' => $nmn->id,
            'ordered_qty' => 20.0000,
            'received_qty' => 20.0000,
            'supplier_lot' => 'LOT-CHROMA-NMN-884',
            'manufacture_date' => now()->subMonths(2)->toDateString(),
            'expiry_date' => now()->addMonths(22)->toDateString(),
            'storage_condition' => StorageCondition::ColdRefrigerated,
        ]);

        // 6. Seed Batches (Approved, Quarantine, Expired, Rejected)
        // Batch 1: Approved NMN batch
        $batchApprovedNmn = RawMaterialBatch::create([
            'raw_material_id' => $nmn->id,
            'batch_number' => 'LOT-RM-202608-0001',
            'supplier_lot' => 'LOT-CHROMA-NMN-884',
            'purchase_order_id' => $po1->id,
            'goods_receipt_id' => $grn1->id,
            'supplier_id' => $chromadex->id,
            'location_id' => $location->id,
            'storage_bin' => 'COLD-RACK-01',
            'initial_qty' => 20.0000,
            'current_qty' => 18.5000,
            'available_qty' => 18.5000,
            'reserved_qty' => 0.0000,
            'quarantine_qty' => 0.0000,
            'unit_cost' => 8400.0000,
            'manufacture_date' => now()->subMonths(2)->toDateString(),
            'expiry_date' => now()->addMonths(22)->toDateString(),
            'status' => RawMaterialBatchStatus::Approved,
        ]);

        // Passed QC inspection for Batch 1
        $qc1 = QcInspection::create([
            'inspection_code' => 'QC-2026-0001',
            'raw_material_batch_id' => $batchApprovedNmn->id,
            'sample_size' => 0.0500,
            'sample_uom' => 'kg',
            'inspector_id' => $chemist->id,
            'verifier_id' => $qaDirector->id,
            'verdict' => QcVerdict::Passed,
            'tested_at' => now()->subDays(30),
            'verified_at' => now()->subDays(29),
            'notes' => 'Conforms to USP/EP monographs. Assay potency 99.85%. Heavy metals non-detectable.',
        ]);

        $qc1->parameters()->createMany([
            ['parameter_name' => 'Assay Potency (%)', 'specification' => '>= 99.0%', 'result_value' => '99.85%', 'status' => 'pass'],
            ['parameter_name' => 'Moisture / Loss on Drying', 'specification' => '< 1.0%', 'result_value' => '0.24%', 'status' => 'pass'],
            ['parameter_name' => 'Heavy Metals (Pb, Cd, As, Hg)', 'specification' => '< 0.5 ppm', 'result_value' => '< 0.05 ppm', 'status' => 'pass'],
            ['parameter_name' => 'Total Microbial Count', 'specification' => '< 100 CFU/g', 'result_value' => '< 10 CFU/g', 'status' => 'pass'],
        ]);

        // Batch 2: Resveratrol Batch in Quarantine
        $batchQuarantineResv = RawMaterialBatch::create([
            'raw_material_id' => $resveratrol->id,
            'batch_number' => 'LOT-RM-202609-0002',
            'supplier_lot' => 'LOT-BIOVIT-RES-912',
            'purchase_order_id' => $po1->id,
            'goods_receipt_id' => $grn1->id,
            'supplier_id' => $biovita->id,
            'location_id' => $location->id,
            'storage_bin' => 'QUARANTINE-AREA-1',
            'initial_qty' => 15.0000,
            'current_qty' => 15.0000,
            'available_qty' => 0.0000,
            'reserved_qty' => 0.0000,
            'quarantine_qty' => 15.0000,
            'unit_cost' => 4150.0000,
            'manufacture_date' => now()->subDays(10)->toDateString(),
            'expiry_date' => now()->addMonths(24)->toDateString(),
            'status' => RawMaterialBatchStatus::Quarantine,
        ]);

        // Batch 3: Near-Expiry Resveratrol (for testing 45-day expiry report)
        $batchExpiringResv = RawMaterialBatch::create([
            'raw_material_id' => $resveratrol->id,
            'batch_number' => 'LOT-RM-202410-0099',
            'supplier_lot' => 'LOT-OLD-RES-110',
            'purchase_order_id' => null,
            'goods_receipt_id' => null,
            'supplier_id' => $biovita->id,
            'location_id' => $location->id,
            'storage_bin' => 'COLD-RACK-03',
            'initial_qty' => 10.0000,
            'current_qty' => 5.2500,
            'available_qty' => 5.2500,
            'reserved_qty' => 0.0000,
            'quarantine_qty' => 0.0000,
            'unit_cost' => 4200.0000,
            'manufacture_date' => now()->subYears(2)->toDateString(),
            'expiry_date' => now()->addDays(40)->toDateString(), // Expiring in 40 days!
            'status' => RawMaterialBatchStatus::Approved,
        ]);

        // Batch 4: Rejected Batch (Failed QC)
        $batchRejected = RawMaterialBatch::create([
            'raw_material_id' => $resveratrol->id,
            'batch_number' => 'LOT-RM-202607-0044',
            'supplier_lot' => 'LOT-FAILED-RES-001',
            'purchase_order_id' => null,
            'goods_receipt_id' => null,
            'supplier_id' => $biovita->id,
            'location_id' => $location->id,
            'storage_bin' => 'REJECTED-LOCKER',
            'initial_qty' => 8.0000,
            'current_qty' => 8.0000,
            'available_qty' => 0.0000,
            'reserved_qty' => 0.0000,
            'quarantine_qty' => 8.0000,
            'unit_cost' => 4150.0000,
            'manufacture_date' => now()->subMonths(3)->toDateString(),
            'expiry_date' => now()->addMonths(20)->toDateString(),
            'status' => RawMaterialBatchStatus::Rejected,
        ]);

        // 7. Seed BOM Recipe for Finished Product
        $product = Product::first();
        if ($product) {
            ProductRecipeItem::updateOrCreate([
                'product_id' => $product->id,
                'raw_material_id' => $nmn->id,
            ], [
                'quantity_per_unit' => 0.000500, // 500mg per unit
                'loss_percentage' => 1.50,
                'is_critical' => true,
                'instructions' => 'Weigh in clean-room glovebox under dry nitrogen atmosphere.',
            ]);

            ProductRecipeItem::updateOrCreate([
                'product_id' => $product->id,
                'raw_material_id' => $capsules->id,
            ], [
                'quantity_per_unit' => 60.000000, // 60 capsules per bottle
                'loss_percentage' => 2.00,
                'is_critical' => true,
                'instructions' => 'Load into semi-automatic encapsulation hopper.',
            ]);

            ProductRecipeItem::updateOrCreate([
                'product_id' => $product->id,
                'raw_material_id' => $bottles->id,
            ], [
                'quantity_per_unit' => 1.000000,
                'loss_percentage' => 0.50,
                'is_critical' => true,
                'instructions' => 'In-line visual inspection before automated fill.',
            ]);

            // 8. Seed Work Order & Consumptions for Genealogy Traceability
            $workOrder = WorkOrder::create([
                'order_number' => 'WO-2026-0001',
                'product_id' => $product->id,
                'target_finished_lot' => 'LOT-FG-202609-0001',
                'planned_qty' => 1000,
                'produced_qty' => 1000,
                'rejected_qty' => 5,
                'status' => WorkOrderStatus::Completed,
                'location_id' => $location->id,
                'scheduled_start' => now()->subDays(15),
                'started_at' => now()->subDays(15)->addHours(2),
                'completed_at' => now()->subDays(14),
                'total_material_cost' => 4200.0000,
                'unit_cost' => 4.2000,
                'created_by' => $admin->id,
                'notes' => 'Validation master batch executed cleanly.',
            ]);

            WorkOrderMaterialConsumption::create([
                'work_order_id' => $workOrder->id,
                'raw_material_batch_id' => $batchApprovedNmn->id,
                'quantity' => 0.5075, // 500mg * 1000 + scrap
                'operator_id' => $admin->id,
                'scanned_barcode' => $batchApprovedNmn->batch_number,
                'created_at' => now()->subDays(15),
            ]);
        }
    }
}
