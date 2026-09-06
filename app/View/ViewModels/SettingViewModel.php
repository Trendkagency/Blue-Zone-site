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
        ];
    }
}
