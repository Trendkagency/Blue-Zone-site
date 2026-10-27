<?php

namespace App\Services;

use App\Models\Setting;

class InvoiceTemplateService
{
    public const SETTING_KEY = 'invoice_template_config';

    /**
     * Get the default invoice template configuration matching the official spreadsheet layout.
     */
    public static function defaultTemplate(): array
    {
        return [
            // Company Header & Contact
            'company_name_en' => 'Blue Zone Pharmaceuticals',
            'company_name_ar' => 'بلو زون للأدوية والمستحضرات الطبية',
            'tagline_en' => 'Leading Cellular Longevity & Botanical Medicine',
            'tagline_ar' => 'الريادة في صحة الخلايا والطب الحيوي',
            'logo_url' => '/images/logo.png',
            'address_en' => 'Riyadh, Saudi Arabia - Olaya Bio-Medical District',
            'address_ar' => 'المملكة العربية السعودية - الرياض - حي العليا الطبي',
            'phone' => '+966 800 123 4567',
            'email' => 'sales@bluezonepharma.com',
            'vat_number' => '31004829100003',
            'cr_number' => '1010884920',

            // Title Banner
            'title_ar' => 'فاتورة مبيعات',
            'title_en' => 'Sales Tax Invoice',
            'show_dual_title' => true,

            // Sections Order (Draggable Blocks)
            'sections_order' => [
                'header_company',
                'client_and_qr',
                'title_banner',
                'items_table',
                'totals_and_payments',
                'signatures_stamp',
                'footer_notes',
            ],

            // 11-Column Items Table Customizer
            'columns' => [
                'row_number' => [
                    'key' => 'row_number',
                    'visible' => true,
                    'label_ar' => 'م',
                    'label_en' => 'N',
                    'width' => '4%',
                    'align' => 'center',
                ],
                'item_code' => [
                    'key' => 'item_code',
                    'visible' => true,
                    'label_ar' => 'كود الصنف',
                    'label_en' => 'Item Code',
                    'width' => '10%',
                    'align' => 'center',
                ],
                'item_description' => [
                    'key' => 'item_description',
                    'visible' => true,
                    'label_ar' => 'اسم الصنف',
                    'label_en' => 'Item Description',
                    'width' => '24%',
                    'align' => 'start',
                ],
                'quantity' => [
                    'key' => 'quantity',
                    'visible' => true,
                    'label_ar' => 'الكمية',
                    'label_en' => 'Quantity',
                    'width' => '7%',
                    'align' => 'center',
                ],
                'batch_expiry' => [
                    'key' => 'batch_expiry',
                    'visible' => true,
                    'label_ar' => 'تاريخ الصلاحية / التشغيلة',
                    'label_en' => 'Expiry Date / Batch No.',
                    'width' => '13%',
                    'align' => 'center',
                ],
                'unit_price' => [
                    'key' => 'unit_price',
                    'visible' => true,
                    'label_ar' => 'سعر الوحدة',
                    'label_en' => 'Price',
                    'width' => '8%',
                    'align' => 'center',
                ],
                'disc_1' => [
                    'key' => 'disc_1',
                    'visible' => true,
                    'label_ar' => 'نسبة الخصم 1',
                    'label_en' => 'Disc. 1',
                    'width' => '6%',
                    'align' => 'center',
                ],
                'disc_2' => [
                    'key' => 'disc_2',
                    'visible' => true,
                    'label_ar' => 'نسبة الخصم 2',
                    'label_en' => 'Disc. 2',
                    'width' => '6%',
                    'align' => 'center',
                ],
                'total_exc_vat' => [
                    'key' => 'total_exc_vat',
                    'visible' => true,
                    'label_ar' => 'إجمالي بدون الضريبة',
                    'label_en' => 'Total Exc. VAT',
                    'width' => '10%',
                    'align' => 'center',
                ],
                'vat_amount' => [
                    'key' => 'vat_amount',
                    'visible' => true,
                    'label_ar' => 'الضريبة',
                    'label_en' => 'VAT',
                    'width' => '6%',
                    'align' => 'center',
                ],
                'total_inc_vat' => [
                    'key' => 'total_inc_vat',
                    'visible' => true,
                    'label_ar' => 'إجمالي الصنف شامل الضريبة',
                    'label_en' => 'Total Inc. VAT',
                    'width' => '11%',
                    'align' => 'center',
                ],
            ],

            // Payment & Tender Box
            'payment_box' => [
                'enabled' => true,
                'title_ar' => 'بيانات وطريقة السداد',
                'title_en' => 'Payment Settlement',
                'methods' => [
                    'cash' => ['key' => 'cash', 'enabled' => true, 'label_ar' => 'نقداً', 'label_en' => 'Cash'],
                    'bank_transfer' => ['key' => 'bank_transfer', 'enabled' => true, 'label_ar' => 'تحويل بنكي', 'label_en' => 'Bank Transfer'],
                    'cheque' => ['key' => 'cheque', 'enabled' => true, 'label_ar' => 'شيك', 'label_en' => 'Cheque'],
                    'partial_payment' => ['key' => 'partial_payment', 'enabled' => true, 'label_ar' => 'دفع جزئي', 'label_en' => 'Partial Payment'],
                ],
                'show_paid_amount' => true,
                'show_due_amount' => true,
                'show_due_date' => true,
                'show_bank_reference' => true,
            ],

            // Financial Summary Labels
            'totals_box' => [
                'label_subtotal_ar' => 'إجمالي الفاتورة بعد خصم الأصناف',
                'label_subtotal_en' => 'Subtotal After Item Discounts',
                'label_extra_disc_pct_ar' => 'خصم إضافي (%)',
                'label_extra_disc_pct_en' => 'Additional Discount (%)',
                'label_extra_disc_val_ar' => 'قيمة الخصم الإضافي',
                'label_extra_disc_val_en' => 'Additional Discount Value',
                'label_net_before_vat_ar' => 'صافي الفاتورة قبل الضريبة VAT',
                'label_net_before_vat_en' => 'Net Total Before VAT',
                'label_vat_ar' => 'قيمة الضريبة VAT (15%)',
                'label_vat_en' => 'VAT Amount (15%)',
                'label_grand_total_ar' => 'إجمالي الفاتورة شامل الضريبة',
                'label_grand_total_en' => 'Grand Total Inc. VAT',
            ],

            // Signatures & Official Stamp
            'signatures' => [
                'enabled' => true,
                'receiver_label_ar' => 'المستلم',
                'receiver_label_en' => 'Receiver Name',
                'signature_label_ar' => 'التوقيع',
                'signature_label_en' => 'Signature',
                'stamp_label_ar' => 'الختم',
                'stamp_label_en' => 'Official Seal',
                'stamp_image' => '',
            ],

            // Footer Policy & Terms
            'footer' => [
                'enabled' => true,
                'terms_ar' => 'البضاعة المباعة لا ترد ولا تستبدل إلا وفق الشروط المعتمدة للأدوية الحيوية وسلاسل التبريد.',
                'terms_en' => 'Temperature-controlled pharmaceutical goods are subject to SFDA handling protocols.',
                'closing_note_ar' => 'شكراً لتعاملكم مع شركة بلو زون للأدوية والمستحضرات الطبية.',
                'closing_note_en' => 'Thank you for partnering with Blue Zone Pharmaceuticals.',
            ],

            // Visual Styling Tokens
            'styling' => [
                'header_bg' => '#d8e1e8',
                'border_color' => '#1e293b',
                'font_family' => 'Tajawal, Outfit, sans-serif',
                'grid_lines' => 'sharp',
            ],
        ];
    }

    /**
     * Retrieve the currently saved template configuration merged with system defaults.
     */
    public static function getTemplate(): array
    {
        $default = static::defaultTemplate();
        $saved = Setting::get(static::SETTING_KEY, null);

        if (empty($saved)) {
            return $default;
        }

        if (is_string($saved)) {
            $decoded = json_decode($saved, true);
            if (is_array($decoded)) {
                return array_replace_recursive($default, $decoded);
            }
        }

        if (is_array($saved)) {
            return array_replace_recursive($default, $saved);
        }

        return $default;
    }

    /**
     * Persist invoice template configuration.
     */
    public static function saveTemplate(array $config): void
    {
        Setting::set(static::SETTING_KEY, json_encode($config, JSON_UNESCAPED_UNICODE), 'invoice_template', 'json');
    }

    /**
     * Reset invoice template configuration back to system default.
     */
    public static function resetToDefault(): void
    {
        Setting::set(static::SETTING_KEY, json_encode(static::defaultTemplate(), JSON_UNESCAPED_UNICODE), 'invoice_template', 'json');
    }

    /**
     * Provide mock order data for the interactive live preview in Admin Settings.
     */
    public static function getPreviewOrder(): array
    {
        return [
            'order_number' => 'ORD-2026-9812',
            'invoice_number' => 'INV-2026-04281',
            'invoice_date' => now()->format('Y-m-d'),
            'date' => now()->format('Y-m-d'),
            'channel' => 'B2B Clinical Distribution',
            'customer_name' => 'Dr. Khalid Al-Mansoor Medical Complex',
            'customer_phone' => '+966 50 123 4567',
            'customer_email' => 'procurement@almansoor-clinic.com',
            'customer_address' => 'King Fahd Road, Al Olaya District, Riyadh, KSA',
            'payment_method' => 'bank_transfer',
            'payment_status' => 'Partially Paid',
            'subtotal' => 4500.00,
            'discount' => 200.00,
            'tax' => 645.00,
            'total' => 4945.00,
            'paid_amount' => 3000.00,
            'due_balance' => 1945.00,
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'bank_reference' => 'WIRE-SNB-9940128',
            'items' => [
                [
                    'sku' => 'BZ-NMN-100',
                    'name_en' => 'Cellular Longevity NMN Pure Formulation 500mg',
                    'name_ar' => 'مركب إن إم إن النقي لدعم حيوية الخلايا 500 مجم',
                    'batch_number' => 'LOT-2026-NMN44',
                    'expiry_date' => '2028-11-30',
                    'quantity' => 10,
                    'price' => 250.00,
                    'disc_1' => 5,
                    'disc_2' => 0,
                    'subtotal' => 2375.00,
                    'tax' => 356.25,
                    'total' => 2731.25,
                ],
                [
                    'sku' => 'BZ-RES-250',
                    'name_en' => 'Trans-Resveratrol Bio-Available Active 250mg',
                    'name_ar' => 'ريسفيراترول عالي الامتصاص لمكافحة الأكسدة 250 مجم',
                    'batch_number' => 'LOT-2026-RES12',
                    'expiry_date' => '2028-09-15',
                    'quantity' => 8,
                    'price' => 150.00,
                    'disc_1' => 0,
                    'disc_2' => 0,
                    'subtotal' => 1200.00,
                    'tax' => 180.00,
                    'total' => 1380.00,
                ],
                [
                    'sku' => 'BZ-NAD-INF',
                    'name_en' => 'Clinical NAD+ Precursor Cellular Injectable Grade',
                    'name_ar' => 'مركب إن إيه دي بلس عالي النقاوة الصيدلانية',
                    'batch_number' => 'LOT-2026-NAD09',
                    'expiry_date' => '2027-12-31',
                    'quantity' => 5,
                    'price' => 225.00,
                    'disc_1' => 10,
                    'disc_2' => 2,
                    'subtotal' => 992.25,
                    'tax' => 148.84,
                    'total' => 1141.09,
                ],
            ],
        ];
    }
}
