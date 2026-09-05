<?php

namespace App\View\ViewModels;

class InventoryViewModel
{
    /**
     * Available inventory locations (prepared for future multi-location expansion).
     *
     * @return array<int, array<string, string>>
     */
    public static function locations(): array
    {
        return [
            ['id' => 'online', 'name_en' => 'Online Fulfillment Hub', 'name_ar' => 'مستودع الطلبات الإلكترونية', 'code' => 'LOC-ONL'],
            ['id' => 'offline', 'name_en' => 'Flagship Boutique / POS', 'name_ar' => 'المتجر الرئيسي / المبيعات المباشرة', 'code' => 'LOC-POS'],
            ['id' => 'central_wh', 'name_en' => 'Central Quarantine Warehouse', 'name_ar' => 'المستودع المركزي الرئيسي', 'code' => 'LOC-CWH'],
        ];
    }

    /**
     * Stock details across products and locations.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function stockItems(): array
    {
        return [
            [
                'id' => 1,
                'product_id' => 1,
                'product_name_en' => 'BLUE MIND',
                'product_name_ar' => 'بلو مايند',
                'sku' => 'BZ-MND-001',
                'variant_en' => '60 Veg Capsules',
                'variant_ar' => '60 كبسولة نباتية',
                'location_id' => 'online',
                'location_name_en' => 'Online Hub',
                'location_name_ar' => 'مستودع المتجر الإلكتروني',
                'current_stock' => 124,
                'available_stock' => 118,
                'reserved_stock' => 6,
                'low_stock_threshold' => 15,
                'status' => 'in_stock', // in_stock, low_stock, out_of_stock
                'unit_cost' => 24.50,
                'retail_price' => 68.00,
            ],
            [
                'id' => 2,
                'product_id' => 1,
                'product_name_en' => 'BLUE MIND',
                'product_name_ar' => 'بلو مايند',
                'sku' => 'BZ-MND-001',
                'variant_en' => '60 Veg Capsules',
                'variant_ar' => '60 كبسولة نباتية',
                'location_id' => 'offline',
                'location_name_en' => 'Flagship Boutique',
                'location_name_ar' => 'المتجر الرئيسي',
                'current_stock' => 38,
                'available_stock' => 38,
                'reserved_stock' => 0,
                'low_stock_threshold' => 10,
                'status' => 'in_stock',
                'unit_cost' => 24.50,
                'retail_price' => 68.00,
            ],
            [
                'id' => 3,
                'product_id' => 2,
                'product_name_en' => 'BLUE CELL',
                'product_name_ar' => 'بلو سيل',
                'sku' => 'BZ-CEL-002',
                'variant_en' => '60 Veg Capsules',
                'variant_ar' => '60 كبسولة نباتية',
                'location_id' => 'online',
                'location_name_en' => 'Online Hub',
                'location_name_ar' => 'مستودع المتجر الإلكتروني',
                'current_stock' => 96,
                'available_stock' => 92,
                'reserved_stock' => 4,
                'low_stock_threshold' => 12,
                'status' => 'in_stock',
                'unit_cost' => 28.00,
                'retail_price' => 74.00,
            ],
            [
                'id' => 4,
                'product_id' => 2,
                'product_name_en' => 'BLUE CELL',
                'product_name_ar' => 'بلو سيل',
                'sku' => 'BZ-CEL-002',
                'variant_en' => '60 Veg Capsules',
                'variant_ar' => '60 كبسولة نباتية',
                'location_id' => 'offline',
                'location_name_en' => 'Flagship Boutique',
                'location_name_ar' => 'المتجر الرئيسي',
                'current_stock' => 8,
                'available_stock' => 8,
                'reserved_stock' => 0,
                'low_stock_threshold' => 12,
                'status' => 'low_stock',
                'unit_cost' => 28.00,
                'retail_price' => 74.00,
            ],
            [
                'id' => 5,
                'product_id' => 4,
                'product_name_en' => 'BLUE METABOLIC',
                'product_name_ar' => 'بلو ميتابوليك',
                'sku' => 'BZ-MET-004',
                'variant_en' => '60 Capsules',
                'variant_ar' => '60 كبسولة',
                'location_id' => 'offline',
                'location_name_en' => 'Flagship Boutique',
                'location_name_ar' => 'المتجر الرئيسي',
                'current_stock' => 3,
                'available_stock' => 3,
                'reserved_stock' => 0,
                'low_stock_threshold' => 10,
                'status' => 'low_stock',
                'unit_cost' => 22.00,
                'retail_price' => 59.00,
            ],
            [
                'id' => 6,
                'product_id' => 6,
                'product_name_en' => 'BLUE VITALITY',
                'product_name_ar' => 'بلو فايتاليتي',
                'sku' => 'BZ-VIT-006',
                'variant_en' => '60 Capsules',
                'variant_ar' => '60 كبسولة',
                'location_id' => 'offline',
                'location_name_en' => 'Flagship Boutique',
                'location_name_ar' => 'المتجر الرئيسي',
                'current_stock' => 0,
                'available_stock' => 0,
                'reserved_stock' => 0,
                'low_stock_threshold' => 12,
                'status' => 'out_of_stock',
                'unit_cost' => 21.00,
                'retail_price' => 62.00,
            ],
        ];
    }

    /**
     * Stock movements audit log.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function movements(): array
    {
        return [
            [
                'id' => 'MOV-2026-0891',
                'product_name_en' => 'BLUE MIND',
                'product_name_ar' => 'بلو مايند',
                'sku' => 'BZ-MND-001',
                'variant_en' => '60 Veg Capsules',
                'variant_ar' => '60 كبسولة نباتية',
                'from_location' => 'Central Quarantine Warehouse',
                'to_location' => 'Online Fulfillment Hub',
                'quantity' => 50,
                'movement_type' => 'Stock Transfer',
                'previous_quantity' => 74,
                'new_quantity' => 124,
                'date' => '2026-09-02',
                'time' => '14:23:10',
                'user' => 'Omar Al-Mansoor (Inventory Lead)',
                'reason' => 'Routine weekly replenishment for online surge',
            ],
            [
                'id' => 'MOV-2026-0890',
                'product_name_en' => 'BLUE CELL',
                'product_name_ar' => 'بلو سيل',
                'sku' => 'BZ-CEL-002',
                'variant_en' => '60 Veg Capsules',
                'variant_ar' => '60 كبسولة نباتية',
                'from_location' => 'Online Fulfillment Hub',
                'to_location' => 'Customer (Order #BZ-10492)',
                'quantity' => -2,
                'movement_type' => 'Online Sale',
                'previous_quantity' => 98,
                'new_quantity' => 96,
                'date' => '2026-09-02',
                'time' => '11:15:44',
                'user' => 'System (Automated Checkout)',
                'reason' => 'Fulfilled verified e-commerce checkout',
            ],
            [
                'id' => 'MOV-2026-0889',
                'product_name_en' => 'BLUE DEFENSE',
                'product_name_ar' => 'بلو ديفينس',
                'sku' => 'BZ-DEF-003',
                'variant_en' => '60 Veg Capsules',
                'variant_ar' => '60 كبسولة نباتية',
                'from_location' => 'Flagship Boutique / POS',
                'to_location' => 'Walk-in Customer (Sale #POS-4081)',
                'quantity' => -1,
                'movement_type' => 'Offline Sale',
                'previous_quantity' => 56,
                'new_quantity' => 55,
                'date' => '2026-09-01',
                'time' => '18:40:02',
                'user' => 'Sarah Jenkins (Boutique Specialist)',
                'reason' => 'POS counter walk-in transaction',
            ],
            [
                'id' => 'MOV-2026-0888',
                'product_name_en' => 'BLUE VITALITY',
                'product_name_ar' => 'بلو فايتاليتي',
                'sku' => 'BZ-VIT-006',
                'variant_en' => '60 Capsules',
                'variant_ar' => '60 كبسولة',
                'from_location' => 'Flagship Boutique / POS',
                'to_location' => 'Damaged Goods Log',
                'quantity' => -1,
                'movement_type' => 'Damaged',
                'previous_quantity' => 1,
                'new_quantity' => 0,
                'date' => '2026-09-01',
                'time' => '09:12:30',
                'user' => 'Omar Al-Mansoor (Inventory Lead)',
                'reason' => 'Damaged seal identified during morning shelf inspection',
            ],
            [
                'id' => 'MOV-2026-0887',
                'product_name_en' => 'BLUE METABOLIC',
                'product_name_ar' => 'بلو ميتابوليك',
                'sku' => 'BZ-MET-004',
                'variant_en' => '60 Capsules',
                'variant_ar' => '60 كبسولة',
                'from_location' => 'Customer Return',
                'to_location' => 'Central Quarantine Warehouse',
                'quantity' => 1,
                'movement_type' => 'Return',
                'previous_quantity' => 24,
                'new_quantity' => 25,
                'date' => '2026-08-31',
                'time' => '16:05:19',
                'user' => 'Khalid Al-Sayed (CS Lead)',
                'reason' => 'Customer unopened return (wrong variant ordered)',
            ],
        ];
    }
}
