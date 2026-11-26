<?php

namespace App\View\ViewModels;

class InventoryViewModel
{
    /**
     * Active inventory hubs (Two-Hub Model: Online Fulfillment Hub & Central Warehouse).
     *
     * @return array<int, array<string, string>>
     */
    public static function locations(): array
    {
        return [
            ['id' => 'online', 'name_en' => 'Online Fulfillment Hub', 'name_ar' => 'مستودع الطلبات الإلكترونية', 'code' => 'LOC-ONL'],
            ['id' => 'central_wh', 'name_en' => 'Central Logistics & Quarantine Warehouse', 'name_ar' => 'المستودع المركزي الرئيسي', 'code' => 'LOC-CWH'],
        ];
    }

    /**
     * Stock balances for the 4 authoritative Blue Zone products across both active hubs.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function stockItems(): array
    {
        return [
            [
                'product_id' => 1,
                'product_name_en' => 'Blue Joint',
                'product_name_ar' => 'بلو جوينت',
                'sku' => 'BZ-JNT-001',
                'variant_en' => '60 Tablets Pack',
                'variant_ar' => 'عبوة 60 قرصاً',
                'location_id' => 'online',
                'location_name_en' => 'Online Fulfillment Hub',
                'location_name_ar' => 'مستودع الطلبات الإلكترونية',
                'current_stock' => 150,
                'available_stock' => 145,
                'reserved_stock' => 5,
                'low_stock_threshold' => 15,
                'status' => 'in_stock',
                'unit_cost' => 25.00,
                'retail_price' => 65.00,
            ],
            [
                'product_id' => 1,
                'product_name_en' => 'Blue Joint',
                'product_name_ar' => 'بلو جوينت',
                'sku' => 'BZ-JNT-001',
                'variant_en' => '60 Tablets Pack',
                'variant_ar' => 'عبوة 60 قرصاً',
                'location_id' => 'central_wh',
                'location_name_en' => 'Central Logistics & Quarantine Warehouse',
                'location_name_ar' => 'المستودع المركزي الرئيسي',
                'current_stock' => 180,
                'available_stock' => 180,
                'reserved_stock' => 0,
                'low_stock_threshold' => 25,
                'status' => 'in_stock',
                'unit_cost' => 25.00,
                'retail_price' => 65.00,
            ],
            [
                'product_id' => 2,
                'product_name_en' => 'Blue Mind',
                'product_name_ar' => 'بلو مايند',
                'sku' => 'BZ-MND-001',
                'variant_en' => '30 Tablets Pack',
                'variant_ar' => 'عبوة 30 قرصاً',
                'location_id' => 'online',
                'location_name_en' => 'Online Fulfillment Hub',
                'location_name_ar' => 'مستودع الطلبات الإلكترونية',
                'current_stock' => 124,
                'available_stock' => 118,
                'reserved_stock' => 6,
                'low_stock_threshold' => 15,
                'status' => 'in_stock',
                'unit_cost' => 28.00,
                'retail_price' => 68.00,
            ],
            [
                'product_id' => 2,
                'product_name_en' => 'Blue Mind',
                'product_name_ar' => 'بلو مايند',
                'sku' => 'BZ-MND-001',
                'variant_en' => '30 Tablets Pack',
                'variant_ar' => 'عبوة 30 قرصاً',
                'location_id' => 'central_wh',
                'location_name_en' => 'Central Logistics & Quarantine Warehouse',
                'location_name_ar' => 'المستودع المركزي الرئيسي',
                'current_stock' => 220,
                'available_stock' => 220,
                'reserved_stock' => 0,
                'low_stock_threshold' => 25,
                'status' => 'in_stock',
                'unit_cost' => 28.00,
                'retail_price' => 68.00,
            ],
            [
                'product_id' => 3,
                'product_name_en' => 'Blue PEA B Complex',
                'product_name_ar' => 'بلو بي إي إيه بي كومبلكس',
                'sku' => 'BZ-PEA-001',
                'variant_en' => '60 Veg Capsules',
                'variant_ar' => '60 كبسولة نباتية',
                'location_id' => 'online',
                'location_name_en' => 'Online Fulfillment Hub',
                'location_name_ar' => 'مستودع الطلبات الإلكترونية',
                'current_stock' => 110,
                'available_stock' => 110,
                'reserved_stock' => 0,
                'low_stock_threshold' => 15,
                'status' => 'in_stock',
                'unit_cost' => 29.00,
                'retail_price' => 72.00,
            ],
            [
                'product_id' => 3,
                'product_name_en' => 'Blue PEA B Complex',
                'product_name_ar' => 'بلو بي إي إيه بي كومبلكس',
                'sku' => 'BZ-PEA-001',
                'variant_en' => '60 Veg Capsules',
                'variant_ar' => '60 كبسولة نباتية',
                'location_id' => 'central_wh',
                'location_name_en' => 'Central Logistics & Quarantine Warehouse',
                'location_name_ar' => 'المستودع المركزي الرئيسي',
                'current_stock' => 160,
                'available_stock' => 160,
                'reserved_stock' => 0,
                'low_stock_threshold' => 25,
                'status' => 'in_stock',
                'unit_cost' => 29.00,
                'retail_price' => 72.00,
            ],
            [
                'product_id' => 4,
                'product_name_en' => 'Blue Shield',
                'product_name_ar' => 'بلو شيلد',
                'sku' => 'BZ-SHD-001',
                'variant_en' => '60 Capsules',
                'variant_ar' => '60 كبسولة',
                'location_id' => 'online',
                'location_name_en' => 'Online Fulfillment Hub',
                'location_name_ar' => 'مستودع الطلبات الإلكترونية',
                'current_stock' => 180,
                'available_stock' => 180,
                'reserved_stock' => 0,
                'low_stock_threshold' => 20,
                'status' => 'in_stock',
                'unit_cost' => 22.00,
                'retail_price' => 58.00,
            ],
            [
                'product_id' => 4,
                'product_name_en' => 'Blue Shield',
                'product_name_ar' => 'بلو شيلد',
                'sku' => 'BZ-SHD-001',
                'variant_en' => '60 Capsules',
                'variant_ar' => '60 كبسولة',
                'location_id' => 'central_wh',
                'location_name_en' => 'Central Logistics & Quarantine Warehouse',
                'location_name_ar' => 'المستودع المركزي الرئيسي',
                'current_stock' => 250,
                'available_stock' => 250,
                'reserved_stock' => 0,
                'low_stock_threshold' => 25,
                'status' => 'in_stock',
                'unit_cost' => 22.00,
                'retail_price' => 58.00,
            ],
        ];
    }

    /**
     * Realistic stock movements audit log across the 4 authoritative SKUs.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function movements(): array
    {
        return [
            [
                'id' => 'MOV-2026-0891',
                'product_name_en' => 'Blue Mind',
                'product_name_ar' => 'بلو مايند',
                'sku' => 'BZ-MND-001',
                'variant_en' => '30 Tablets Pack',
                'variant_ar' => 'عبوة 30 قرصاً',
                'from_location' => 'Central Logistics & Quarantine Warehouse',
                'to_location' => 'Online Fulfillment Hub',
                'quantity' => 50,
                'movement_type' => 'Stock Transfer',
                'previous_qty' => 74,
                'new_qty' => 124,
                'date' => now()->subDays(3)->toDateString(),
                'time' => '14:23:10',
                'user' => 'Omar Fadhel (Inventory Staff)',
                'note' => 'FEFO batch replenishment transferred from Central Hub to Online Hub',
            ],
            [
                'id' => 'MOV-2026-0890',
                'product_name_en' => 'Blue Joint',
                'product_name_ar' => 'بلو جوينت',
                'sku' => 'BZ-JNT-001',
                'variant_en' => '60 Tablets Pack',
                'variant_ar' => 'عبوة 60 قرصاً',
                'from_location' => 'Online Fulfillment Hub',
                'to_location' => 'Customer (Order #BZ-10492)',
                'quantity' => -2,
                'movement_type' => 'Sales Issue',
                'previous_qty' => 152,
                'new_qty' => 150,
                'date' => now()->subDays(2)->toDateString(),
                'time' => '11:15:44',
                'user' => 'System (FEFO Engine)',
                'note' => 'FEFO batch deduction for verified e-commerce order',
            ],
            [
                'id' => 'MOV-2026-0889',
                'product_name_en' => 'Blue PEA B Complex',
                'product_name_ar' => 'بلو بي إي إيه بي كومبلكس',
                'sku' => 'BZ-PEA-001',
                'variant_en' => '60 Veg Capsules',
                'variant_ar' => '60 كبسولة نباتية',
                'from_location' => 'Clean Room Manufacturing (WO-2026-0001)',
                'to_location' => 'Central Logistics & Quarantine Warehouse',
                'quantity' => 160,
                'movement_type' => 'production_receipt',
                'previous_qty' => 0,
                'new_qty' => 160,
                'date' => now()->subDays(14)->toDateString(),
                'time' => '16:10:00',
                'user' => 'Manufacturing System',
                'note' => 'Finished batch output released from Work Order #WO-2026-0001',
            ],
            [
                'id' => 'MOV-2026-0888',
                'product_name_en' => 'Blue Shield',
                'product_name_ar' => 'بلو شيلد',
                'sku' => 'BZ-SHD-001',
                'variant_en' => '60 Capsules',
                'variant_ar' => '60 كبسولة',
                'from_location' => 'Central Logistics & Quarantine Warehouse',
                'to_location' => 'Quarantine / Quality Inspection',
                'quantity' => -2,
                'movement_type' => 'Damaged',
                'previous_qty' => 252,
                'new_qty' => 250,
                'date' => now()->subDays(5)->toDateString(),
                'time' => '09:12:30',
                'user' => 'Dr. Sarah Al-Mansouri (QA)',
                'note' => 'Retention QA stability testing samples pulled from sealed cartons',
            ],
        ];
    }
}
