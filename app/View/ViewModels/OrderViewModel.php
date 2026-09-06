<?php

namespace App\View\ViewModels;

class OrderViewModel
{
    /**
     * Get all mock orders (both online and offline channels).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            [
                'id' => 10492,
                'order_number' => 'BZ-10492',
                'invoice_number' => 'INV-2026-0841',
                'channel' => 'online', // online, offline
                'customer_name' => 'Dr. Zaid Al-Harbi',
                'customer_email' => 'zaid.harbi@example.com',
                'customer_phone' => '+966 50 123 4567',
                'date' => '2026-09-02',
                'status' => 'Processing', // Pending, Confirmed, Processing, Shipped, Delivered, Cancelled, Returned
                'payment_method' => 'Credit Card (Stripe)',
                'payment_status' => 'Paid',
                'subtotal' => 142.00,
                'discount' => 15.00,
                'coupon_code' => 'WELCOME15',
                'shipping' => 0.00,
                'tax' => 19.05,
                'total' => 146.05,
                'shipping_address' => [
                    'recipient' => 'Dr. Zaid Al-Harbi',
                    'street' => 'King Fahd Road, Al Olaya, Villa 42',
                    'city' => 'Riyadh',
                    'country' => 'Saudi Arabia',
                    'postal_code' => '12213',
                ],
                'items' => [
                    [
                        'product_name_en' => 'BLUE MIND',
                        'product_name_ar' => 'بلو مايند',
                        'variant_en' => 'Standard 30-Day Protocol (60 Capsules)',
                        'variant_ar' => 'بروتوكول 30 يوماً القياسي (60 كبسولة)',
                        'sku' => 'BZ-MND-001',
                        'unit_price' => 68.00,
                        'quantity' => 1,
                        'total' => 68.00,
                        'image' => '/assets/products/blue-mind.jpg',
                    ],
                    [
                        'product_name_en' => 'BLUE CELL',
                        'product_name_ar' => 'بلو سيل',
                        'variant_en' => '30-Day Cell Reserve (60 Capsules)',
                        'variant_ar' => 'احتياطي خلايا 30 يوماً (60 كبسولة)',
                        'sku' => 'BZ-CEL-002',
                        'unit_price' => 74.00,
                        'quantity' => 1,
                        'total' => 74.00,
                        'image' => '/assets/products/blue-cell.jpg',
                    ],
                ],
                'timeline' => [
                    ['status' => 'Pending', 'timestamp' => '2026-09-02 10:14:02', 'note' => 'Order received and checkout authenticated.'],
                    ['status' => 'Confirmed', 'timestamp' => '2026-09-02 10:14:45', 'note' => 'Payment authorized via 3D Secure.'],
                    ['status' => 'Processing', 'timestamp' => '2026-09-02 11:20:10', 'note' => 'Dispatched to fulfillment picking batch.'],
                ],
            ],
            [
                'id' => 10491,
                'order_number' => 'BZ-10491',
                'invoice_number' => 'INV-2026-0840',
                'channel' => 'online',
                'customer_name' => 'Layla Bint Sultan',
                'customer_email' => 'layla.sultan@example.com',
                'customer_phone' => '+971 55 987 6543',
                'date' => '2026-09-01',
                'status' => 'Shipped',
                'payment_method' => 'Apple Pay',
                'payment_status' => 'Paid',
                'subtotal' => 114.00,
                'discount' => 10.00,
                'coupon_code' => 'LONGEVITY10',
                'shipping' => 15.00,
                'tax' => 17.85,
                'total' => 136.85,
                'shipping_address' => [
                    'recipient' => 'Layla Bint Sultan',
                    'street' => 'Marina Gate 1, Apt 1804',
                    'city' => 'Dubai',
                    'country' => 'United Arab Emirates',
                    'postal_code' => '00000',
                ],
                'items' => [
                    [
                        'product_name_en' => 'BLUE DEFENSE',
                        'product_name_ar' => 'بلو ديفينس',
                        'variant_en' => 'Standard Bottle (60 Capsules)',
                        'variant_ar' => 'العبوة القياسية (60 كبسولة)',
                        'sku' => 'BZ-DEF-003',
                        'unit_price' => 52.00,
                        'quantity' => 1,
                        'total' => 52.00,
                        'image' => '/assets/products/blue-defense.jpg',
                    ],
                    [
                        'product_name_en' => 'BLUE VITALITY',
                        'product_name_ar' => 'بلو فايتاليتي',
                        'variant_en' => 'Standard Vitality Bottle (60 Capsules)',
                        'variant_ar' => 'العبوة القياسية (60 كبسولة)',
                        'sku' => 'BZ-VIT-006',
                        'unit_price' => 62.00,
                        'quantity' => 1,
                        'total' => 62.00,
                        'image' => '/assets/products/blue-vitality.jpg',
                    ],
                ],
                'timeline' => [
                    ['status' => 'Pending', 'timestamp' => '2026-09-01 08:30:11', 'note' => 'Order received.'],
                    ['status' => 'Confirmed', 'timestamp' => '2026-09-01 08:31:00', 'note' => 'Card payment captured.'],
                    ['status' => 'Processing', 'timestamp' => '2026-09-01 10:00:00', 'note' => 'Packed in cold-chain bio-box.'],
                    ['status' => 'Shipped', 'timestamp' => '2026-09-01 14:45:00', 'note' => 'Courier AWB #DHL-8899124 dispatched.'],
                ],
            ],
            [
                'id' => 10490,
                'order_number' => 'BZ-10490',
                'invoice_number' => 'INV-2026-0839',
                'channel' => 'online',
                'customer_name' => 'Tariq Al-Ghamdi',
                'customer_email' => 'tariq.g@example.com',
                'customer_phone' => '+966 54 332 1199',
                'date' => '2026-08-30',
                'status' => 'Delivered',
                'payment_method' => 'Cash on Delivery (COD)',
                'payment_status' => 'Paid',
                'subtotal' => 48.00,
                'discount' => 0.00,
                'coupon_code' => null,
                'shipping' => 15.00,
                'tax' => 9.45,
                'total' => 72.45,
                'shipping_address' => [
                    'recipient' => 'Tariq Al-Ghamdi',
                    'street' => 'Al Hamra District, Al Andalus St',
                    'city' => 'Jeddah',
                    'country' => 'Saudi Arabia',
                    'postal_code' => '23212',
                ],
                'items' => [
                    [
                        'product_name_en' => 'BLUE SLEEP',
                        'product_name_ar' => 'بلو سليب',
                        'variant_en' => '30 Nights Slumber Pack (60 Capsules)',
                        'variant_ar' => 'عبوة 30 ليلة نوم هانئ (60 كبسولة)',
                        'sku' => 'BZ-SLP-005',
                        'unit_price' => 48.00,
                        'quantity' => 1,
                        'total' => 48.00,
                        'image' => '/assets/products/blue-sleep.jpg',
                    ],
                ],
                'timeline' => [
                    ['status' => 'Pending', 'timestamp' => '2026-08-30 19:22:00', 'note' => 'Order created with COD.'],
                    ['status' => 'Confirmed', 'timestamp' => '2026-08-30 20:00:00', 'note' => 'Phone verification confirmed.'],
                    ['status' => 'Processing', 'timestamp' => '2026-08-31 09:00:00', 'note' => 'Packed for dispatch.'],
                    ['status' => 'Shipped', 'timestamp' => '2026-08-31 13:00:00', 'note' => 'Out for delivery with driver.'],
                    ['status' => 'Delivered', 'timestamp' => '2026-09-01 16:30:00', 'note' => 'Delivered and COD collected in full.'],
                ],
            ],
        ];
    }

    /**
     * Offline Sales / POS Transactions.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function offlineSales(): array
    {
        return [
            [
                'id' => 4081,
                'sale_number' => 'POS-4081',
                'invoice_number' => 'INV-POS-4081',
                'cashier' => 'Sarah Jenkins',
                'store_location' => 'Flagship Boutique / POS',
                'customer_name' => 'Walk-in Guest (Fahad M.)',
                'date' => '2026-09-01',
                'time' => '18:40:02',
                'payment_method' => 'Credit / POS Terminal (Mada)',
                'subtotal' => 52.00,
                'discount' => 0.00,
                'tax' => 7.80,
                'total' => 59.80,
                'items' => [
                    [
                        'product_name_en' => 'BLUE DEFENSE',
                        'product_name_ar' => 'بلو ديفينس',
                        'sku' => 'BZ-DEF-003',
                        'unit_price' => 52.00,
                        'quantity' => 1,
                        'total' => 52.00,
                    ],
                ],
            ],
            [
                'id' => 4080,
                'sale_number' => 'POS-4080',
                'invoice_number' => 'INV-POS-4080',
                'cashier' => 'Sarah Jenkins',
                'store_location' => 'Flagship Boutique / POS',
                'customer_name' => 'Walk-in Guest (VIP Member)',
                'date' => '2026-09-01',
                'time' => '15:20:18',
                'payment_method' => 'Cash',
                'subtotal' => 142.00,
                'discount' => 14.20,
                'tax' => 19.17,
                'total' => 146.97,
                'items' => [
                    [
                        'product_name_en' => 'BLUE MIND',
                        'product_name_ar' => 'بلو مايند',
                        'sku' => 'BZ-MND-001',
                        'unit_price' => 68.00,
                        'quantity' => 1,
                        'total' => 68.00,
                    ],
                    [
                        'product_name_en' => 'BLUE CELL',
                        'product_name_ar' => 'بلو سيل',
                        'sku' => 'BZ-CEL-002',
                        'unit_price' => 74.00,
                        'quantity' => 1,
                        'total' => 74.00,
                    ],
                ],
            ],
        ];
    }

    /**
     * Find order by number or ID.
     */
    public static function find(string|int $identifier): ?array
    {
        foreach (self::all() as $order) {
            if ($order['id'] == $identifier || $order['order_number'] === (string)$identifier) {
                return $order;
            }
        }
        return self::all()[0] ?? null;
    }
}
