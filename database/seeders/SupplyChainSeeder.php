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
use App\Models\ProductBatch;
use App\Models\ProductRecipeItem;
use App\Models\PurchaseOrder;
use App\Models\QcInspection;
use App\Models\RawMaterial;
use App\Models\RawMaterialBatch;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\SupplierMaterialPrice;
use App\Models\SupplierQuotation;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderMaterialConsumption;
use Illuminate\Database\Seeder;

class SupplyChainSeeder extends Seeder
{
    public function run(): void
    {
        $sysCurrency = Setting::get('currency', 'EGP');

        // 1. Supply Chain Specialists (preserving Super Admin Role IDs)
        $admin = User::where('email', 'admin@bluezone.com')->first()
            ?? User::first()
            ?? User::factory()->create([
                'name' => 'Dr. Zaid Al-Nuaimi',
                'email' => 'zaid@bluezone.com',
                'password' => bcrypt('password'),
                'status' => 'active',
            ]);

        $qaDirector = User::updateOrCreate(['email' => 'qa@bluezone.com'], [
            'name' => 'Dr. Sarah Al-Mansouri (QA Director)',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $procurementHead = User::updateOrCreate(['email' => 'procurement@bluezone.com'], [
            'name' => 'Eng. Tariq Al-Hashimi (Procurement)',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $chemist = User::updateOrCreate(['email' => 'chemist@bluezone.com'], [
            'name' => 'Analyst Karim Mahmoud (Chemist)',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        // Call SupplyChainRoleSeeder again to bind dedicated roles to these created users
        (new SupplyChainRoleSeeder())->run();

        $country = Country::where('iso2', 'EG')->first()
            ?? Country::where('iso2', 'SA')->first()
            ?? Country::first()
            ?? Country::create([
                'iso2' => 'EG',
                'name_en' => 'Egypt',
                'name_ar' => 'مصر',
                'phone_code' => '+20',
                'currency_code' => 'EGP',
                'currency_symbol_en' => 'EGP',
                'currency_symbol_ar' => 'ج.م',
                'is_active' => true,
            ]);

        $location = Location::where('id', 'central_wh')->first() ?? Location::create([
            'id' => 'central_wh',
            'name_en' => 'Central Logistics & Quarantine Warehouse',
            'name_ar' => 'المستودع المركزي الرئيسي',
            'code' => 'LOC-CWH',
            'type' => 'warehouse',
            'is_active' => true,
        ]);

        // 2. Seed Dual-Approved Suppliers + Pending Review Vendor
        $chromadex = Supplier::updateOrCreate(['code' => 'SUP-CHROMA-01'], [
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
            'currency' => $sysCurrency,
            'lead_time_days' => 12,
            'is_active' => true,
            'qa_approved_by' => $qaDirector->id,
            'qa_approved_at' => now()->subMonths(3),
            'procurement_approved_by' => $procurementHead->id,
            'procurement_approved_at' => now()->subMonths(3),
        ]);

        $biovita = Supplier::updateOrCreate(['code' => 'SUP-BIOVIT-02'], [
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
            'currency' => $sysCurrency,
            'lead_time_days' => 16,
            'is_active' => true,
            'qa_approved_by' => $qaDirector->id,
            'qa_approved_at' => now()->subMonths(2),
            'procurement_approved_by' => $procurementHead->id,
            'procurement_approved_at' => now()->subMonths(2),
        ]);

        $eastpack = Supplier::updateOrCreate(['code' => 'SUP-EASTPK-03'], [
            'type' => SupplierType::PackagingVendor,
            'name_en' => 'EastPack Amber Glass Co.',
            'name_ar' => 'شركة إيست باك للزجاج والعبوات الدوائية',
            'contact_name' => 'Li Wei',
            'email' => 'export@eastpack-glass.com',
            'phone' => '+862158880199',
            'country_id' => $country->id,
            'city' => 'Cairo',
            'address' => '10th of Ramadan Industrial Zone B',
            'rating' => 4.45,
            'gmp_certified' => true,
            'iso_certified' => true,
            'audit_expiry' => now()->addMonths(10)->toDateString(),
            'payment_terms' => 'NET60',
            'currency' => $sysCurrency,
            'lead_time_days' => 21,
            'is_active' => true,
            'qa_approved_by' => $qaDirector->id,
            'qa_approved_at' => now()->subMonths(1),
            'procurement_approved_by' => $procurementHead->id,
            'procurement_approved_at' => now()->subMonths(1),
        ]);

        Supplier::updateOrCreate(['code' => 'SUP-NEWLAB-04'], [
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
            'currency' => $sysCurrency,
            'lead_time_days' => 20,
            'is_active' => false,
            'qa_approved_by' => $qaDirector->id,
            'qa_approved_at' => now(),
        ]);

        // 3. Raw Materials covering all 4 Flagship Formulations
        $glucosamine = RawMaterial::updateOrCreate(['code' => 'RM-GLUC-01'], [
            'name_en' => 'Glucosamine HCl Pharmaceutical Grade 99%',
            'name_ar' => 'جلوكوزامين هيدروكلوريد صيدلاني 99%',
            'inci_name' => 'Glucosamine Hydrochloride',
            'cas_number' => '66-84-2',
            'category' => RawMaterialCategory::ActiveIngredient,
            'unit_of_measure' => UnitOfMeasure::Kilogram,
            'storage_condition' => StorageCondition::Ambient,
            'standard_cost' => 1450.0000,
            'min_reorder_qty' => 20.0000,
            'safety_stock' => 10.0000,
            'shelf_life_days' => 730,
            'requires_coa' => true,
            'default_supplier_id' => $chromadex->id,
            'is_active' => true,
        ]);

        $boswellia = RawMaterial::updateOrCreate(['code' => 'RM-BOSW-02'], [
            'name_en' => 'Boswellia Serrata Extract (65% Boswellic Acids)',
            'name_ar' => 'مستخلص البوزويليا المنشاري (65% أحماض البوزويليك)',
            'inci_name' => 'Boswellia Serrata Resin Extract',
            'cas_number' => '97952-72-2',
            'category' => RawMaterialCategory::ActiveIngredient,
            'unit_of_measure' => UnitOfMeasure::Kilogram,
            'storage_condition' => StorageCondition::Ambient,
            'standard_cost' => 2200.0000,
            'min_reorder_qty' => 10.0000,
            'safety_stock' => 5.0000,
            'shelf_life_days' => 730,
            'requires_coa' => true,
            'default_supplier_id' => $biovita->id,
            'is_active' => true,
        ]);

        $ginkgo = RawMaterial::updateOrCreate(['code' => 'RM-GINK-03'], [
            'name_en' => 'Standardized Ginkgo Biloba Extract 24/6',
            'name_ar' => 'مستخلص الجنكة بيلوبا الموحد 24/6',
            'inci_name' => 'Ginkgo Biloba Leaf Extract',
            'cas_number' => '90045-36-6',
            'category' => RawMaterialCategory::ActiveIngredient,
            'unit_of_measure' => UnitOfMeasure::Kilogram,
            'storage_condition' => StorageCondition::ColdRefrigerated,
            'standard_cost' => 3100.0000,
            'min_reorder_qty' => 8.0000,
            'safety_stock' => 4.0000,
            'shelf_life_days' => 730,
            'requires_coa' => true,
            'default_supplier_id' => $biovita->id,
            'is_active' => true,
        ]);

        $peaPowder = RawMaterial::updateOrCreate(['code' => 'RM-PEA-04'], [
            'name_en' => 'Micronized Palmitoylethanolamide (PEA 99.5%)',
            'name_ar' => 'بالميتويل إيثانولاميد ميكروني (PEA 99.5%)',
            'inci_name' => 'Palmitoylethanolamide',
            'cas_number' => '544-31-0',
            'category' => RawMaterialCategory::ActiveIngredient,
            'unit_of_measure' => UnitOfMeasure::Kilogram,
            'storage_condition' => StorageCondition::ColdRefrigerated,
            'standard_cost' => 6400.0000,
            'min_reorder_qty' => 10.0000,
            'safety_stock' => 4.0000,
            'shelf_life_days' => 730,
            'requires_coa' => true,
            'default_supplier_id' => $chromadex->id,
            'is_active' => true,
        ]);

        $vitD3K2 = RawMaterial::updateOrCreate(['code' => 'RM-VD3K2-05'], [
            'name_en' => 'Vitamin D3 100k IU/g + MenaQ7 K2 MK-7 Premix',
            'name_ar' => 'خليط فيتامين D3 + ميناكينون-7 K2 MK-7 النقي',
            'inci_name' => 'Cholecalciferol & Menaquinone-7',
            'cas_number' => '67-97-0',
            'category' => RawMaterialCategory::ActiveIngredient,
            'unit_of_measure' => UnitOfMeasure::Kilogram,
            'storage_condition' => StorageCondition::ColdRefrigerated,
            'standard_cost' => 4800.0000,
            'min_reorder_qty' => 6.0000,
            'safety_stock' => 3.0000,
            'shelf_life_days' => 1095,
            'requires_coa' => true,
            'default_supplier_id' => $biovita->id,
            'is_active' => true,
        ]);

        $capsules = RawMaterial::updateOrCreate(['code' => 'RM-CAPS-00'], [
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

        $bottles = RawMaterial::updateOrCreate(['code' => 'RM-BOTT-60'], [
            'name_en' => 'Pharmaceutical Amber Glass Bottle 60ml + Cap',
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

        // 4. Supplier Material Prices
        foreach ([
            [$chromadex->id, $glucosamine->id, 1420.00, 20.0, 12],
            [$biovita->id, $boswellia->id, 2150.00, 10.0, 16],
            [$biovita->id, $ginkgo->id, 3050.00, 8.0, 14],
            [$chromadex->id, $peaPowder->id, 6350.00, 10.0, 12],
            [$biovita->id, $vitD3K2->id, 4750.00, 5.0, 16],
            [$eastpack->id, $capsules->id, 0.0750, 50000.0, 18],
            [$eastpack->id, $bottles->id, 1.8000, 3000.0, 18],
        ] as [$supId, $rmId, $price, $moq, $lead]) {
            SupplierMaterialPrice::updateOrCreate(
                ['supplier_id' => $supId, 'raw_material_id' => $rmId],
                [
                    'unit_price' => $price,
                    'currency' => $sysCurrency,
                    'min_order_qty' => $moq,
                    'lead_time_days' => $lead,
                    'is_preferred' => true,
                ]
            );
        }

        // 5. Supplier Quotation (RFQ -> Converted PO & Open Pending RFQ)
        $sq1 = SupplierQuotation::updateOrCreate(['quote_number' => 'SQ-2026-0001'], [
            'supplier_id' => $chromadex->id,
            'rfq_reference' => 'RFQ-Q4-PEA-01',
            'quote_date' => now()->subDays(48)->toDateString(),
            'valid_until' => now()->subDays(18)->toDateString(),
            'status' => 'converted',
            'currency' => $sysCurrency,
            'exchange_rate' => 1.0000,
            'subtotal' => 127000.00,
            'tax_amount' => 19050.00,
            'estimated_freight' => 2500.00,
            'estimated_customs' => 4500.00,
            'grand_total' => 153050.00,
            'payment_terms' => 'NET30',
            'lead_time_days' => 12,
            'notes' => 'Accepted RFQ for Q4 PEA micronized raw active batch.',
            'created_by' => $procurementHead->id,
        ]);

        SupplierQuotation::updateOrCreate(['quote_number' => 'SQ-2026-0002'], [
            'supplier_id' => $biovita->id,
            'rfq_reference' => 'RFQ-2026-D3K2',
            'quote_date' => now()->subDays(3)->toDateString(),
            'valid_until' => now()->addDays(27)->toDateString(),
            'status' => 'pending',
            'currency' => $sysCurrency,
            'exchange_rate' => 1.0000,
            'subtotal' => 47500.00,
            'tax_amount' => 7125.00,
            'estimated_freight' => 1200.00,
            'estimated_customs' => 1800.00,
            'grand_total' => 57625.00,
            'payment_terms' => 'NET30',
            'lead_time_days' => 14,
            'notes' => 'Under engineering & procurement price review.',
            'created_by' => $procurementHead->id,
        ]);

        // 6. Purchase Orders & Goods Receipts (PO-1 Received, PO-2 Confirmed / Pending Intake)
        $po1 = PurchaseOrder::updateOrCreate(['po_number' => 'PO-2026-0001'], [
            'supplier_id' => $chromadex->id,
            'supplier_quotation_id' => $sq1->id,
            'order_date' => now()->subDays(45)->toDateString(),
            'expected_date' => now()->subDays(30)->toDateString(),
            'destination_location_id' => $location->id,
            'status' => PurchaseOrderStatus::Received,
            'subtotal' => 127000.00,
            'freight_cost' => 2500.00,
            'customs_duties' => 4500.00,
            'tax_amount' => 19050.00,
            'grand_total' => 153050.00,
            'currency' => $sysCurrency,
            'created_by' => $procurementHead->id,
            'approved_by' => $admin->id,
            'approved_at' => now()->subDays(44),
        ]);
        $sq1->update(['converted_po_id' => $po1->id]);

        $po1Item = $po1->items()->updateOrCreate(
            ['purchase_order_id' => $po1->id, 'raw_material_id' => $peaPowder->id],
            [
                'item_type' => 'raw_material',
                'ordered_qty' => 20.0000,
                'received_qty' => 20.0000,
                'unit_price' => 6350.0000,
                'total_price' => 127000.00,
                'allocated_landed_cost' => 7000.00,
                'landed_unit_cost' => 6700.0000,
            ]
        );

        $grn1 = GoodsReceipt::updateOrCreate(['grn_number' => 'GRN-2026-0001'], [
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

        $grn1->items()->updateOrCreate(
            ['goods_receipt_id' => $grn1->id, 'raw_material_id' => $peaPowder->id],
            [
                'item_type' => 'raw_material',
                'purchase_order_item_id' => $po1Item->id,
                'ordered_qty' => 20.0000,
                'received_qty' => 20.0000,
                'supplier_lot' => 'LOT-CHROMA-PEA-884',
                'manufacture_date' => now()->subMonths(2)->toDateString(),
                'expiry_date' => now()->addMonths(22)->toDateString(),
                'storage_condition' => StorageCondition::ColdRefrigerated,
            ]
        );

        // Open Confirmed Purchase Order waiting on warehouse dock
        $po2 = PurchaseOrder::updateOrCreate(['po_number' => 'PO-2026-0002'], [
            'supplier_id' => $biovita->id,
            'order_date' => now()->subDays(5)->toDateString(),
            'expected_date' => now()->addDays(4)->toDateString(),
            'destination_location_id' => $location->id,
            'status' => PurchaseOrderStatus::Confirmed,
            'subtotal' => 42900.00,
            'freight_cost' => 1100.00,
            'customs_duties' => 1900.00,
            'tax_amount' => 6435.00,
            'grand_total' => 52335.00,
            'currency' => $sysCurrency,
            'created_by' => $procurementHead->id,
            'approved_by' => $admin->id,
            'approved_at' => now()->subDays(4),
        ]);
        $po2->items()->updateOrCreate(
            ['purchase_order_id' => $po2->id, 'raw_material_id' => $boswellia->id],
            [
                'item_type' => 'raw_material',
                'ordered_qty' => 20.0000,
                'received_qty' => 0.0000,
                'unit_price' => 2145.0000,
                'total_price' => 42900.00,
                'allocated_landed_cost' => 3000.00,
                'landed_unit_cost' => 2295.0000,
            ]
        );

        // 7. Seed Raw Material Batches (Approved, Quarantine, Near-Expiry, Rejected)
        $batchApprovedPea = RawMaterialBatch::updateOrCreate(['batch_number' => 'LOT-RM-202609-0001'], [
            'raw_material_id' => $peaPowder->id,
            'supplier_lot' => 'LOT-CHROMA-PEA-884',
            'purchase_order_id' => $po1->id,
            'goods_receipt_id' => $grn1->id,
            'supplier_id' => $chromadex->id,
            'location_id' => $location->id,
            'storage_bin' => 'COLD-RACK-01',
            'initial_qty' => 20.0000,
            'current_qty' => 18.0000,
            'available_qty' => 18.0000,
            'reserved_qty' => 0.0000,
            'quarantine_qty' => 0.0000,
            'unit_cost' => 6700.0000,
            'manufacture_date' => now()->subMonths(2)->toDateString(),
            'expiry_date' => now()->addMonths(22)->toDateString(),
            'status' => RawMaterialBatchStatus::Approved,
        ]);

        RawMaterialBatch::updateOrCreate(['batch_number' => 'LOT-RM-202609-0002'], [
            'raw_material_id' => $glucosamine->id,
            'supplier_lot' => 'LOT-GLUC-551',
            'supplier_id' => $chromadex->id,
            'location_id' => $location->id,
            'storage_bin' => 'AMBIENT-RACK-04',
            'initial_qty' => 60.0000,
            'current_qty' => 54.0000,
            'available_qty' => 50.0000,
            'reserved_qty' => 4.0000,
            'quarantine_qty' => 0.0000,
            'unit_cost' => 1450.0000,
            'manufacture_date' => now()->subMonths(3)->toDateString(),
            'expiry_date' => now()->addMonths(21)->toDateString(),
            'status' => RawMaterialBatchStatus::Approved,
        ]);

        RawMaterialBatch::updateOrCreate(['batch_number' => 'LOT-RM-202610-0003'], [
            'raw_material_id' => $ginkgo->id,
            'supplier_lot' => 'LOT-GINK-DE-302',
            'supplier_id' => $biovita->id,
            'location_id' => $location->id,
            'storage_bin' => 'COLD-RACK-02',
            'initial_qty' => 16.0000,
            'current_qty' => 16.0000,
            'available_qty' => 16.0000,
            'reserved_qty' => 0.0000,
            'quarantine_qty' => 0.0000,
            'unit_cost' => 3100.0000,
            'manufacture_date' => now()->subMonth()->toDateString(),
            'expiry_date' => now()->addMonths(23)->toDateString(),
            'status' => RawMaterialBatchStatus::Approved,
        ]);

        RawMaterialBatch::updateOrCreate(['batch_number' => 'LOT-RM-202610-0004'], [
            'raw_material_id' => $vitD3K2->id,
            'supplier_lot' => 'LOT-VD3K2-DE-118',
            'supplier_id' => $biovita->id,
            'location_id' => $location->id,
            'storage_bin' => 'COLD-RACK-03',
            'initial_qty' => 12.0000,
            'current_qty' => 12.0000,
            'available_qty' => 12.0000,
            'reserved_qty' => 0.0000,
            'quarantine_qty' => 0.0000,
            'unit_cost' => 4800.0000,
            'manufacture_date' => now()->subMonths(2)->toDateString(),
            'expiry_date' => now()->addMonths(34)->toDateString(),
            'status' => RawMaterialBatchStatus::Approved,
        ]);

        RawMaterialBatch::updateOrCreate(['batch_number' => 'LOT-RM-202610-0005'], [
            'raw_material_id' => $capsules->id,
            'supplier_lot' => 'LOT-CAPS-PK-901',
            'supplier_id' => $eastpack->id,
            'location_id' => $location->id,
            'storage_bin' => 'PACK-ZONE-A',
            'initial_qty' => 100000.0000,
            'current_qty' => 88000.0000,
            'available_qty' => 88000.0000,
            'reserved_qty' => 0.0000,
            'quarantine_qty' => 0.0000,
            'unit_cost' => 0.0800,
            'manufacture_date' => now()->subMonths(4)->toDateString(),
            'expiry_date' => now()->addMonths(48)->toDateString(),
            'status' => RawMaterialBatchStatus::Approved,
        ]);

        RawMaterialBatch::updateOrCreate(['batch_number' => 'LOT-RM-202610-0006'], [
            'raw_material_id' => $bottles->id,
            'supplier_lot' => 'LOT-BTL-PK-401',
            'supplier_id' => $eastpack->id,
            'location_id' => $location->id,
            'storage_bin' => 'PACK-ZONE-B',
            'initial_qty' => 10000.0000,
            'current_qty' => 9200.0000,
            'available_qty' => 9200.0000,
            'reserved_qty' => 0.0000,
            'quarantine_qty' => 0.0000,
            'unit_cost' => 1.8500,
            'manufacture_date' => now()->subMonths(4)->toDateString(),
            'expiry_date' => now()->addMonths(60)->toDateString(),
            'status' => RawMaterialBatchStatus::Approved,
        ]);

        // Quarantine & Near-Expiry Batches
        RawMaterialBatch::updateOrCreate(['batch_number' => 'LOT-RM-202611-0007'], [
            'raw_material_id' => $boswellia->id,
            'supplier_lot' => 'LOT-BIOVIT-BSW-912',
            'supplier_id' => $biovita->id,
            'location_id' => $location->id,
            'storage_bin' => 'QUARANTINE-AREA-1',
            'initial_qty' => 15.0000,
            'current_qty' => 15.0000,
            'available_qty' => 0.0000,
            'reserved_qty' => 0.0000,
            'quarantine_qty' => 15.0000,
            'unit_cost' => 2200.0000,
            'manufacture_date' => now()->subDays(10)->toDateString(),
            'expiry_date' => now()->addMonths(24)->toDateString(),
            'status' => RawMaterialBatchStatus::Quarantine,
        ]);

        RawMaterialBatch::updateOrCreate(['batch_number' => 'LOT-RM-202411-0099'], [
            'raw_material_id' => $boswellia->id,
            'supplier_lot' => 'LOT-OLD-BSW-110',
            'supplier_id' => $biovita->id,
            'location_id' => $location->id,
            'storage_bin' => 'FEFO-ALERT-01',
            'initial_qty' => 10.0000,
            'current_qty' => 5.2500,
            'available_qty' => 5.2500,
            'reserved_qty' => 0.0000,
            'quarantine_qty' => 0.0000,
            'unit_cost' => 2100.0000,
            'manufacture_date' => now()->subMonths(23)->toDateString(),
            'expiry_date' => now()->addDays(38)->toDateString(),
            'status' => RawMaterialBatchStatus::Approved,
        ]);

        // QC Inspections
        $qc1 = QcInspection::updateOrCreate(['inspection_code' => 'QC-2026-0001'], [
            'raw_material_batch_id' => $batchApprovedPea->id,
            'sample_size' => 0.0500,
            'sample_uom' => 'kg',
            'inspector_id' => $chemist->id,
            'verifier_id' => $qaDirector->id,
            'verdict' => QcVerdict::Passed,
            'tested_at' => now()->subDays(30),
            'verified_at' => now()->subDays(29),
            'notes' => 'Conforms to USP/EP monographs. Assay potency 99.7%. Heavy metals non-detectable.',
        ]);
        if ($qc1->parameters()->doesntExist()) {
            $qc1->parameters()->createMany([
                ['parameter_name' => 'Assay Potency (%)', 'specification' => '>= 99.0%', 'result_value' => '99.70%', 'status' => 'pass'],
                ['parameter_name' => 'Moisture / Loss on Drying', 'specification' => '< 1.0%', 'result_value' => '0.24%', 'status' => 'pass'],
                ['parameter_name' => 'Heavy Metals (Pb, Cd, As, Hg)', 'specification' => '< 0.5 ppm', 'result_value' => '< 0.05 ppm', 'status' => 'pass'],
            ]);
        }

        // 8. BOM Recipes for ALL 4 Authoritative Products
        $pJoint = Product::where('sku', 'BZ-JNT-001')->first();
        $pMind = Product::where('sku', 'BZ-MND-001')->first();
        $pPea = Product::where('sku', 'BZ-PEA-001')->first();
        $pShield = Product::where('sku', 'BZ-SHD-001')->first();

        $bomMap = [
            [$pJoint, [[$glucosamine->id, 0.001000, 1.5, 'Weigh active glucosamine in dry booth'], [$boswellia->id, 0.000330, 1.5, 'Blend standardized boswellia'], [$capsules->id, 60.0, 2.0, '60 tablets/caps count'], [$bottles->id, 1.0, 0.5, 'Seal container']]],
            [$pMind, [[$ginkgo->id, 0.000120, 1.5, 'Sift standardized Ginkgo biloba 24/6'], [$capsules->id, 30.0, 2.0, '30 count blister/bottle'], [$bottles->id, 1.0, 0.5, 'Seal container']]],
            [$pPea, [[$peaPowder->id, 0.000800, 1.5, 'Micronized PEA 800mg cleanroom dosing'], [$capsules->id, 60.0, 2.0, 'Vegetable HPMC capsule fill'], [$bottles->id, 1.0, 0.5, 'Amber bottle + childproof cap']]],
            [$pShield, [[$vitD3K2->id, 0.000200, 1.0, 'Blend D3+K2 under nitrogen shield'], [$capsules->id, 60.0, 2.0, 'Vegetable capsule fill'], [$bottles->id, 1.0, 0.5, 'Amber bottle + cap']]],
        ];

        foreach ($bomMap as [$prod, $ingredients]) {
            if (!$prod) {
                continue;
            }
            foreach ($ingredients as [$rmId, $qtyPerUnit, $lossPct, $instr]) {
                ProductRecipeItem::updateOrCreate(
                    ['product_id' => $prod->id, 'raw_material_id' => $rmId],
                    ['quantity_per_unit' => $qtyPerUnit, 'loss_percentage' => $lossPct, 'is_critical' => true, 'instructions' => $instr]
                );
            }
        }

        // 9. Work Orders: Completed + In Production + Scheduled
        if ($pPea) {
            $woCompleted = WorkOrder::updateOrCreate(['order_number' => 'WO-2026-0001'], [
                'product_id' => $pPea->id,
                'target_finished_lot' => 'LOT-FG-202610-0001',
                'planned_qty' => 500,
                'produced_qty' => 500,
                'rejected_qty' => 4,
                'status' => WorkOrderStatus::Completed,
                'location_id' => $location->id,
                'scheduled_start' => now()->subDays(16),
                'started_at' => now()->subDays(15),
                'completed_at' => now()->subDays(14),
                'total_material_cost' => 3600.0000,
                'landed_costs' => [
                    ['category' => 'Cleanroom Direct Labor', 'amount' => 450.00, 'note' => 'Shift A cleanroom encapsulation crew'],
                    ['category' => 'Third-Party Lab HPLC Assay', 'amount' => 300.00, 'note' => 'Release stability verification'],
                ],
                'total_landed_cost' => 750.0000,
                'unit_cost' => 8.7000,
                'created_by' => $admin->id,
                'notes' => 'Master cleanroom validation batch executed with full genealogy & landed overhead.',
            ]);

            WorkOrderMaterialConsumption::updateOrCreate(
                ['work_order_id' => $woCompleted->id, 'raw_material_batch_id' => $batchApprovedPea->id],
                ['quantity' => 0.4060, 'operator_id' => $chemist->id, 'scanned_barcode' => $batchApprovedPea->batch_number]
            );

            ProductBatch::updateOrCreate(
                ['batch_number' => $woCompleted->target_finished_lot],
                [
                    'product_id' => $pPea->id,
                    'work_order_id' => $woCompleted->id,
                    'location_id' => $location->id,
                    'storage_bin' => 'CWH-RELEASED-01',
                    'initial_qty' => 500,
                    'current_qty' => 500,
                    'available_qty' => 500,
                    'reserved_qty' => 0,
                    'quarantine_qty' => 0,
                    'unit_cost' => 8.7000,
                    'manufacture_date' => now()->subDays(14)->toDateString(),
                    'expiry_date' => $pPea->calculateDefaultExpiryDate(now()->subDays(14)),
                    'status' => 'approved',
                ]
            );
        }

        if ($pJoint) {
            WorkOrder::updateOrCreate(['order_number' => 'WO-2026-0002'], [
                'product_id' => $pJoint->id,
                'target_finished_lot' => 'LOT-FG-202611-0002',
                'planned_qty' => 300,
                'status' => WorkOrderStatus::InProduction,
                'location_id' => $location->id,
                'scheduled_start' => now()->subDays(1),
                'started_at' => now()->subHours(6),
                'created_by' => $admin->id,
                'notes' => 'Active tableting line #2 batch for Q4 Blue Joint replenishment.',
            ]);
        }

        if ($pShield) {
            WorkOrder::updateOrCreate(['order_number' => 'WO-2026-0003'], [
                'product_id' => $pShield->id,
                'target_finished_lot' => 'LOT-FG-202611-0003',
                'planned_qty' => 400,
                'status' => WorkOrderStatus::Scheduled,
                'location_id' => $location->id,
                'scheduled_start' => now()->addDays(2),
                'created_by' => $admin->id,
                'notes' => 'Scheduled Vitamin D3+K2 cofactor blend run.',
            ]);
        }
    }
}
