<?php

namespace App\View\ViewModels;

class SettingViewModel
{
    /**
     * Get system configuration settings.
     *
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        return [
<<<<<<< HEAD
            'store_name' => 'BLUE ZONE™ Longevity & Cellular Health',
            'support_email' => 'care@bluezone.com',
            'support_phone' => '+966 800 123 4567',
            'default_currency' => 'USD',
            'tax_percentage' => 15,
            'free_shipping_threshold' => 75.00,
            'default_locale' => 'en',
            'supported_locales' => ['en' => 'English (LTR)', 'ar' => 'العربية (RTL)'],
            'theme_preference' => 'system',
            'inventory_low_stock_global_threshold' => 10,
            'enable_backorders' => false,
            'order_prefix' => 'BZ-',
            'invoice_prefix' => 'INV-',
            'active_payment_methods' => ['Credit Card', 'Apple Pay', 'Mada', 'Cash on Delivery'],
=======
            // General & Brand
            'site_name' => 'BLUE ZONE™ Longevity & Cellular Health',
            'store_name' => 'BLUE ZONE™ Longevity & Cellular Health',
            'tagline' => 'Cellular Longevity & Botanical Medicine',
            'default_language' => 'en',
            'default_locale' => 'en',
            'supported_locales' => ['en' => 'English (LTR)', 'ar' => 'العربية (RTL)'],
            'currency' => 'USD',
            'default_currency' => 'USD',
            'timezone' => 'Asia/Riyadh',
            'contact_email' => 'care@bluezone.com',
            'support_email' => 'care@bluezone.com',
            'contact_phone' => '+966 800 123 4567',
            'support_phone' => '+966 800 123 4567',

            // Store & Inventory
            'low_stock_threshold' => 10,
            'inventory_low_stock_global_threshold' => 10,
            'zero_stock_behavior' => 'mark_out_of_stock',
            'enable_backorders' => false,
            'enable_reviews' => true,
            'enable_coupons' => true,

            // Payments & Tax
            'tax_percentage' => 15,
            'tax_number' => '31004829100003',
            'enable_online_payment' => true,
            'enable_cod' => true,
            'active_payment_methods' => ['Credit Card', 'Apple Pay', 'Mada', 'Cash on Delivery'],

            // Shipping & Logistics
            'free_shipping_threshold' => 75.00,
            'flat_shipping_rate' => 9.99,

            // Notifications
            'notify_low_stock' => true,
            'notify_new_order' => true,

            // System Prefixes & Theme
            'order_prefix' => 'BZ-',
            'invoice_prefix' => 'INV-',
            'theme_preference' => 'light',
>>>>>>> origin/main
        ];
    }
}
