<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Services\InventoryService;
use App\View\ViewModels\CategoryViewModel;
use App\View\ViewModels\InventoryViewModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BluezoneSeeder extends Seeder
{
    public function run(): void
    {
        Cache::flush();

        $this->seedSettings();
        $this->seedRoles();
        $this->seedAdminUser();
        $this->seedCategories();
        $this->seedProducts();
        $this->seedCustomers();
        $this->seedInventory();
        $this->seedOrders();
    }

    /* ================================================================== */
    /* SETTINGS                                                             */
    /* ================================================================== */

    private function seedSettings(): void
    {
        $settings = [
            // General & Brand
            ['key' => 'site_name',                   'value' => 'BLUE ZONE™ Longevity & Cellular Health',   'group' => 'general',       'type' => 'string'],
            ['key' => 'store_name',                  'value' => 'BLUE ZONE™ Longevity & Cellular Health',   'group' => 'general',       'type' => 'string'],
            ['key' => 'tagline',                     'value' => 'Cellular Longevity & Botanical Medicine',   'group' => 'general',       'type' => 'string'],
            ['key' => 'default_language',            'value' => 'en',                                        'group' => 'general',       'type' => 'string'],
            ['key' => 'default_locale',              'value' => 'en',                                        'group' => 'general',       'type' => 'string'],
            ['key' => 'supported_locales',           'value' => json_encode(['en' => 'English (LTR)', 'ar' => 'العربية (RTL)']), 'group' => 'general', 'type' => 'json'],
            ['key' => 'currency',                    'value' => 'EGP',                                       'group' => 'general',       'type' => 'string'],
            ['key' => 'default_currency',            'value' => 'EGP',                                       'group' => 'general',       'type' => 'string'],
            ['key' => 'timezone',                    'value' => 'Africa/Cairo',                              'group' => 'general',       'type' => 'string'],
            ['key' => 'contact_email',               'value' => 'care@bluezone.com',                         'group' => 'general',       'type' => 'string'],
            ['key' => 'support_email',               'value' => 'care@bluezone.com',                         'group' => 'general',       'type' => 'string'],
            ['key' => 'contact_phone',               'value' => '+966 800 123 4567',                         'group' => 'general',       'type' => 'string'],
            ['key' => 'support_phone',               'value' => '+966 800 123 4567',                         'group' => 'general',       'type' => 'string'],
            // WhatsApp
            ['key' => 'enable_whatsapp',             'value' => '1',                                         'group' => 'contact',       'type' => 'boolean'],
            ['key' => 'whatsapp_number',             'value' => '+966501234567',                              'group' => 'contact',       'type' => 'string'],
            ['key' => 'whatsapp_default_message',    'value' => 'Hello BLUE ZONE, I would like clinical guidance on longevity formulations.', 'group' => 'contact', 'type' => 'string'],
            ['key' => 'whatsapp_position',           'value' => 'auto',                                      'group' => 'contact',       'type' => 'string'],
            // Store & Inventory
            ['key' => 'low_stock_threshold',                   'value' => '10',                'group' => 'inventory',     'type' => 'integer'],
            ['key' => 'inventory_low_stock_global_threshold',  'value' => '10',                'group' => 'inventory',     'type' => 'integer'],
            ['key' => 'zero_stock_behavior',                   'value' => 'mark_out_of_stock', 'group' => 'inventory',     'type' => 'string'],
            ['key' => 'enable_backorders',                     'value' => '0',                 'group' => 'inventory',     'type' => 'boolean'],
            ['key' => 'enable_reviews',                        'value' => '1',                 'group' => 'store',         'type' => 'boolean'],
            ['key' => 'enable_coupons',                        'value' => '1',                 'group' => 'store',         'type' => 'boolean'],
            // Payments & Tax
            ['key' => 'tax_percentage',                'value' => '15',        'group' => 'payments', 'type' => 'float'],
            ['key' => 'tax_number',                    'value' => '31004829100003', 'group' => 'payments', 'type' => 'string'],
            ['key' => 'enable_online_payment',         'value' => '1',         'group' => 'payments', 'type' => 'boolean'],
            ['key' => 'enable_cod',                    'value' => '1',         'group' => 'payments', 'type' => 'boolean'],
            ['key' => 'payment_stripe_enabled',        'value' => '1',         'group' => 'payments', 'type' => 'boolean'],
            ['key' => 'payment_stripe_mode',           'value' => 'test',      'group' => 'payments', 'type' => 'string'],
            ['key' => 'payment_stripe_public_key',     'value' => 'pk_test_51MockStripeKeyBlueZoneLongevityDemo',    'group' => 'payments', 'type' => 'string'],
            ['key' => 'payment_stripe_secret_key',     'value' => 'sk_test_51MockStripeSecretBlueZoneLongevityDemo', 'group' => 'payments', 'type' => 'string'],
            ['key' => 'payment_stripe_webhook_secret', 'value' => 'whsec_mockBlueZoneWebhookSecret2026',             'group' => 'payments', 'type' => 'string'],
            ['key' => 'payment_cod_enabled',           'value' => '1',         'group' => 'payments', 'type' => 'boolean'],
            ['key' => 'payment_cod_extra_fee',         'value' => '0',         'group' => 'payments', 'type' => 'float'],
            ['key' => 'payment_default_gateway',       'value' => 'stripe',    'group' => 'payments', 'type' => 'string'],
            ['key' => 'active_payment_methods',        'value' => json_encode(['Credit Card', 'Apple Pay', 'Mada', 'Cash on Delivery']), 'group' => 'payments', 'type' => 'json'],
            // Shipping
            ['key' => 'free_shipping_threshold', 'value' => '75',   'group' => 'shipping',       'type' => 'float'],
            ['key' => 'flat_shipping_rate',      'value' => '9.99', 'group' => 'shipping',       'type' => 'float'],
            // Notifications
            ['key' => 'notify_low_stock',  'value' => '1', 'group' => 'notifications', 'type' => 'boolean'],
            ['key' => 'notify_new_order',  'value' => '1', 'group' => 'notifications', 'type' => 'boolean'],
            // Firebase & FCM Cloud Messaging Default Configuration
            ['key' => 'fcm_project_id',          'value' => 'bluezone-998e6',                             'group' => 'fcm', 'type' => 'string'],
            ['key' => 'fcm_api_key',             'value' => 'AIzaSyCRAdcHPrsQOtHJGW7gMY9ZN1WiIOi8ClU',    'group' => 'fcm', 'type' => 'string'],
            ['key' => 'fcm_auth_domain',         'value' => 'bluezone-998e6.firebaseapp.com',             'group' => 'fcm', 'type' => 'string'],
            ['key' => 'fcm_storage_bucket',      'value' => 'bluezone-998e6.firebasestorage.app',        'group' => 'fcm', 'type' => 'string'],
            ['key' => 'fcm_messaging_sender_id', 'value' => '440590917450',                              'group' => 'fcm', 'type' => 'string'],
            ['key' => 'fcm_app_id',              'value' => '1:440590917450:web:9e289f9a0a36b2f10d2bb3', 'group' => 'fcm', 'type' => 'string'],
            ['key' => 'fcm_measurement_id',     'value' => 'G-JKHXY1LDD8',                               'group' => 'fcm', 'type' => 'string'],
            ['key' => 'fcm_server_key',          'value' => '',                                           'group' => 'fcm', 'type' => 'string'],
            ['key' => 'fcm_vapid_key',           'value' => 'BN_V2qIO7Zn5IyO0W27vogv8CjT0xmzxkBF-Ao7bTuCHh7QQDrWRmxc25W2cnJGAysdhrvQhuykB5bcAq2P0uXI', 'group' => 'fcm', 'type' => 'string'],
            ['key' => 'fcm_notify_low_stock',    'value' => '1',                                          'group' => 'fcm', 'type' => 'boolean'],
            ['key' => 'fcm_notify_out_stock',    'value' => '1',                                          'group' => 'fcm', 'type' => 'boolean'],
            ['key' => 'fcm_notify_transfers',    'value' => '1',                                          'group' => 'fcm', 'type' => 'boolean'],
            ['key' => 'fcm_notify_issues',       'value' => '1',                                          'group' => 'fcm', 'type' => 'boolean'],
            ['key' => 'fcm_sound_enabled',       'value' => '1',                                          'group' => 'fcm', 'type' => 'boolean'],
            // System
            ['key' => 'order_prefix',     'value' => 'BZ-',  'group' => 'system', 'type' => 'string'],
            ['key' => 'invoice_prefix',   'value' => 'INV-', 'group' => 'system', 'type' => 'string'],
            ['key' => 'theme_preference', 'value' => 'dark', 'group' => 'system', 'type' => 'string'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(
                ['key' => $s['key']],
                ['value' => $s['value'], 'group' => $s['group'], 'type' => $s['type']]
            );
        }
    }

    /* ================================================================== */
    /* ROLES                                                                */
    /* ================================================================== */

    private function seedRoles(): void
    {
        $roles = [
            [
                'name'        => 'Super Admin',
                'description' => 'Full system access — manages all data, settings, and users.',
                'permissions' => json_encode(['*']),
                'users_count' => 1,
            ],
            [
                'name'        => 'Manager',
                'description' => 'Manages products, orders, customers and inventory. No system settings access.',
                'permissions' => json_encode(['products.*', 'orders.*', 'customers.*', 'inventory.*', 'reports.view']),
                'users_count' => 2,
            ],
            [
                'name'        => 'Sales Staff',
                'description' => 'Can view and process orders and manage customer relationships.',
                'permissions' => json_encode(['orders.view', 'orders.edit', 'customers.view']),
                'users_count' => 4,
            ],
            [
                'name'        => 'Inventory Staff',
                'description' => 'Manages warehouse stock levels and inventory movements.',
                'permissions' => json_encode(['inventory.*', 'products.view']),
                'users_count' => 3,
            ],
        ];

        foreach ($roles as $r) {
            Role::updateOrCreate(['name' => $r['name']], $r);
        }
    }

    /* ================================================================== */
    /* ADMIN & STAFF USERS                                                  */
    /* ================================================================== */

    private function seedAdminUser(): void
    {
        $adminRole     = Role::where('name', 'Super Admin')->first() ?? Role::first();
        $managerRole   = Role::where('name', 'Manager')->first();
        $salesRole     = Role::where('name', 'Sales Staff')->first();
        $inventoryRole = Role::where('name', 'Inventory Staff')->first();

        $users = [
            ['name' => 'Tariq M.',       'email' => 'admin@bluezone.com',   'role_id' => $adminRole?->id],
            ['name' => 'Super Admin',    'email' => 'admin@admin.com',      'role_id' => $adminRole?->id],
            ['name' => 'Lina Al-Rashid', 'email' => 'lina@bluezone.com',    'role_id' => $managerRole?->id],
            ['name' => 'Khalid Nassir',  'email' => 'khalid@bluezone.com',  'role_id' => $salesRole?->id],
            ['name' => 'Sara Almutairi', 'email' => 'sara@bluezone.com',    'role_id' => $salesRole?->id],
            ['name' => 'Omar Fadhel',    'email' => 'omar@bluezone.com',    'role_id' => $inventoryRole?->id],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                array_merge($u, [
                    'password'          => bcrypt('password'),
                    'status'            => 'active',
                    'email_verified_at' => now(),
                ])
            );
        }
    }

    /* ================================================================== */
    /* CATEGORIES                                                           */
    /* ================================================================== */

    private function seedCategories(): void
    {
        foreach (CategoryViewModel::all() as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name_en'        => $cat['name_en'],
                    'name_ar'        => $cat['name_ar'],
                    'slug'           => $cat['slug'],
                    'icon'           => $cat['icon'] ?? null,
                    'description_en' => $cat['description_en'] ?? null,
                    'description_ar' => $cat['description_ar'] ?? null,
                    'sort_order'     => $cat['sort_order'] ?? 0,
                    'is_active'      => ($cat['status'] ?? 'active') === 'active',
                ]
            );
        }
    }

    /* ================================================================== */
    /* PRODUCTS                                                             */
    /* ================================================================== */

    private function seedProducts(): void
    {
        (new ProductSeeder())->seedProducts();
    }

    /* ================================================================== */
    /* CUSTOMERS                                                            */
    /* ================================================================== */

    /* ================================================================== */
    /* CUSTOMERS                                                            */
    /* ================================================================== */

    private function seedCustomers(): void
    {
        $customersData = [
            [
                'name' => 'Dr. Zaid Al-Harbi',
                'email' => 'zaid.harbi@example.com',
                'phone' => '+966 50 123 4567',
                'address' => 'King Fahd Road, Al Olaya, Villa 42',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'postal_code' => '12213',
                'registered_at' => '2026-01-15',
            ],
            [
                'name' => 'Sarah Al-Mansoor',
                'email' => 'sarah.m@example.com',
                'phone' => '+966 55 987 6543',
                'address' => 'Prince Turki St, Corniche Dist',
                'city' => 'Al Khobar',
                'country' => 'Saudi Arabia',
                'postal_code' => '31952',
                'registered_at' => '2026-02-10',
            ],
            [
                'name' => 'Eng. Tariq Al-Otaibi',
                'email' => 'tariq.otaibi@example.com',
                'phone' => '+966 54 321 0987',
                'address' => 'Al Andalus District, Villa 18',
                'city' => 'Jeddah',
                'country' => 'Saudi Arabia',
                'postal_code' => '23326',
                'registered_at' => '2026-03-05',
            ],
            [
                'name' => 'Dr. Nora Al-Sudairy',
                'email' => 'nora.sudairy@example.com',
                'phone' => '+966 56 444 8899',
                'address' => 'Diplomatic Quarter, Compound 7',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'postal_code' => '12512',
                'registered_at' => '2026-04-12',
            ],
            [
                'name' => 'Faisal Al-Ghamdi',
                'email' => 'faisal.ghamdi@example.com',
                'phone' => '+966 53 777 1122',
                'address' => 'Al Shatie District, Tower 3',
                'city' => 'Jeddah',
                'country' => 'Saudi Arabia',
                'postal_code' => '23513',
                'registered_at' => '2026-05-18',
            ],
            [
                'name' => 'Lama Al-Husseini',
                'email' => 'lama.h@example.com',
                'phone' => '+966 50 888 3344',
                'address' => 'Al Malqa District, St 45',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'postal_code' => '13521',
                'registered_at' => '2026-06-20',
            ],
            [
                'name' => 'Khalid Al-Dossary',
                'email' => 'khalid.d@example.com',
                'phone' => '+966 55 222 9900',
                'address' => 'Al Bandariyah, Villa 12',
                'city' => 'Dammam',
                'country' => 'Saudi Arabia',
                'postal_code' => '32242',
                'registered_at' => '2026-07-02',
            ],
            [
                'name' => 'Dr. Rayan Al-Amri',
                'email' => 'rayan.amri@example.com',
                'phone' => '+966 54 111 5566',
                'address' => 'Al Yasmin District, St 12',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'postal_code' => '13322',
                'registered_at' => '2026-08-01',
            ],
        ];

        foreach ($customersData as $c) {
            Customer::updateOrCreate(
                ['email' => $c['email']],
                [
                    'name'              => $c['name'],
                    'email'             => $c['email'],
                    'password'          => bcrypt('password'),
                    'phone'             => $c['phone'],
                    'address'           => $c['address'],
                    'city'              => $c['city'],
                    'country'           => $c['country'],
                    'postal_code'       => $c['postal_code'],
                    'total_orders'      => 0,
                    'total_spent'       => 0,
                    'status'            => 'active',
                    'email_verified_at' => now(),
                    'registered_at'     => $c['registered_at'],
                ]
            );
        }
    }

    /* ================================================================== */
    /* ORDERS                                                               */
    /* ================================================================== */

    private function seedOrders(): void
    {
        $orders = [
            ['order_number' => 'BZ-10492', 'invoice_number' => 'INV-2026-0841', 'channel' => 'online',    'customer_email' => 'zaid.harbi@example.com',   'customer_name' => 'Dr. Zaid Al-Harbi',     'date' => now()->subDays(2)->toDateString(),  'status' => 'delivered',  'payment_method' => 'Credit Card (Stripe)', 'payment_status' => 'Paid', 'discount' => 15.00, 'coupon_code' => 'WELCOME15',   'shipping' => 0.00,  'items' => [['sku' => 'BZ-JNT-001', 'qty' => 2], ['sku' => 'BZ-MND-001', 'qty' => 1]]],
            ['order_number' => 'BZ-10491', 'invoice_number' => 'INV-2026-0840', 'channel' => 'online',    'customer_email' => 'sarah.m@example.com',      'customer_name' => 'Sarah Al-Mansoor',      'date' => now()->subDays(4)->toDateString(),  'status' => 'processing', 'payment_method' => 'Apple Pay',            'payment_status' => 'Paid', 'discount' => 0.00,  'coupon_code' => null,          'shipping' => 0.00,  'items' => [['sku' => 'BZ-PEA-001', 'qty' => 2], ['sku' => 'BZ-SHD-001', 'qty' => 1]]],
            ['order_number' => 'BZ-10490', 'invoice_number' => 'INV-2026-0839', 'channel' => 'quotation', 'customer_email' => 'tariq.otaibi@example.com', 'customer_name' => 'Eng. Tariq Al-Otaibi',  'date' => now()->subDays(6)->toDateString(),  'status' => 'delivered',  'payment_method' => 'Bank Transfer',        'payment_status' => 'Paid', 'discount' => 30.00, 'coupon_code' => 'B2B30',       'shipping' => 0.00,  'items' => [['sku' => 'BZ-MND-001', 'qty' => 3], ['sku' => 'BZ-SHD-001', 'qty' => 2]]],
            ['order_number' => 'BZ-10489', 'invoice_number' => 'INV-2026-0838', 'channel' => 'online',    'customer_email' => 'nora.sudairy@example.com', 'customer_name' => 'Dr. Nora Al-Sudairy',   'date' => now()->subDays(8)->toDateString(),  'status' => 'delivered',  'payment_method' => 'Bank Transfer',        'payment_status' => 'Paid', 'discount' => 20.00, 'coupon_code' => 'VIP20',       'shipping' => 0.00,  'items' => [['sku' => 'BZ-JNT-001', 'qty' => 1], ['sku' => 'BZ-PEA-001', 'qty' => 1], ['sku' => 'BZ-SHD-001', 'qty' => 1]]],
            ['order_number' => 'BZ-10488', 'invoice_number' => 'INV-2026-0835', 'channel' => 'online',    'customer_email' => 'faisal.ghamdi@example.com','customer_name' => 'Faisal Al-Ghamdi',      'date' => now()->subDays(12)->toDateString(), 'status' => 'shipped',    'payment_method' => 'Credit Card (Stripe)', 'payment_status' => 'Paid', 'discount' => 0.00,  'coupon_code' => null,          'shipping' => 15.00, 'items' => [['sku' => 'BZ-MND-001', 'qty' => 1], ['sku' => 'BZ-PEA-001', 'qty' => 1]]],
            ['order_number' => 'BZ-10480', 'invoice_number' => 'INV-2026-0820', 'channel' => 'online',    'customer_email' => 'zaid.harbi@example.com',   'customer_name' => 'Dr. Zaid Al-Harbi',     'date' => now()->subDays(18)->toDateString(), 'status' => 'delivered',  'payment_method' => 'Credit Card (Stripe)', 'payment_status' => 'Paid', 'discount' => 25.00, 'coupon_code' => 'LOYAL25',     'shipping' => 0.00,  'items' => [['sku' => 'BZ-JNT-001', 'qty' => 3]]],
            ['order_number' => 'BZ-10475', 'invoice_number' => 'INV-2026-0810', 'channel' => 'online',    'customer_email' => 'lama.h@example.com',       'customer_name' => 'Lama Al-Husseini',      'date' => now()->subDays(22)->toDateString(), 'status' => 'delivered',  'payment_method' => 'Apple Pay',            'payment_status' => 'Paid', 'discount' => 0.00,  'coupon_code' => null,          'shipping' => 0.00,  'items' => [['sku' => 'BZ-SHD-001', 'qty' => 2], ['sku' => 'BZ-MND-001', 'qty' => 1]]],
            ['order_number' => 'BZ-10472', 'invoice_number' => 'INV-2026-0805', 'channel' => 'direct',    'customer_email' => 'khalid.d@example.com',     'customer_name' => 'Khalid Al-Dossary',     'date' => now()->subDays(27)->toDateString(), 'status' => 'delivered',  'payment_method' => 'Cash',                 'payment_status' => 'Paid', 'discount' => 0.00,  'coupon_code' => null,          'shipping' => 0.00,  'items' => [['sku' => 'BZ-JNT-001', 'qty' => 1], ['sku' => 'BZ-SHD-001', 'qty' => 1]]],
            ['order_number' => 'BZ-10450', 'invoice_number' => 'INV-2026-0750', 'channel' => 'online',    'customer_email' => 'rayan.amri@example.com',   'customer_name' => 'Dr. Rayan Al-Amri',     'date' => now()->subDays(34)->toDateString(), 'status' => 'delivered',  'payment_method' => 'Credit Card (Stripe)', 'payment_status' => 'Paid', 'discount' => 35.00, 'coupon_code' => 'LONGEVITY35', 'shipping' => 0.00,  'items' => [['sku' => 'BZ-MND-001', 'qty' => 2], ['sku' => 'BZ-PEA-001', 'qty' => 2]]],
            ['order_number' => 'BZ-10410', 'invoice_number' => 'INV-2026-0610', 'channel' => 'online',    'customer_email' => 'nora.sudairy@example.com', 'customer_name' => 'Dr. Nora Al-Sudairy',   'date' => now()->subDays(45)->toDateString(), 'status' => 'delivered',  'payment_method' => 'Apple Pay',            'payment_status' => 'Paid', 'discount' => 0.00,  'coupon_code' => null,          'shipping' => 0.00,  'items' => [['sku' => 'BZ-PEA-001', 'qty' => 2], ['sku' => 'BZ-JNT-001', 'qty' => 1]]],
            ['order_number' => 'BZ-10380', 'invoice_number' => 'INV-2026-0580', 'channel' => 'online',    'customer_email' => 'faisal.ghamdi@example.com','customer_name' => 'Faisal Al-Ghamdi',      'date' => now()->subDays(60)->toDateString(), 'status' => 'delivered',  'payment_method' => 'Credit Card (Stripe)', 'payment_status' => 'Paid', 'discount' => 25.00, 'coupon_code' => 'PROTOCOL25',  'shipping' => 0.00,  'items' => [['sku' => 'BZ-MND-001', 'qty' => 2], ['sku' => 'BZ-SHD-001', 'qty' => 2]]],
            ['order_number' => 'BZ-10100', 'invoice_number' => 'INV-2026-0100', 'channel' => 'online',    'customer_email' => 'zaid.harbi@example.com',   'customer_name' => 'Dr. Zaid Al-Harbi',     'date' => now()->subDays(75)->toDateString(), 'status' => 'delivered',  'payment_method' => 'Bank Transfer',        'payment_status' => 'Paid', 'discount' => 20.00, 'coupon_code' => 'LAUNCH20',    'shipping' => 0.00,  'items' => [['sku' => 'BZ-MND-001', 'qty' => 2], ['sku' => 'BZ-JNT-001', 'qty' => 1], ['sku' => 'BZ-SHD-001', 'qty' => 1]]],
        ];

        $productsBySku = Product::all()->keyBy('sku');

        foreach ($orders as $o) {
            $customer = Customer::where('email', $o['customer_email'] ?? '')->first();

            // Calculate exact financials dynamically from actual catalog items
            $subtotal = 0.0;
            $resolvedLines = [];
            foreach ($o['items'] as $itemData) {
                $product = $productsBySku[$itemData['sku']] ?? null;
                if (!$product) {
                    continue;
                }
                $qty = (int) $itemData['qty'];
                $unitPrice = (float) ($itemData['unit_price'] ?? $product->price);
                $lineTotal = round($unitPrice * $qty, 2);
                $subtotal += $lineTotal;
                $resolvedLines[] = [
                    'product' => $product,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            $discount = (float) ($o['discount'] ?? 0.0);
            $shipping = (float) ($o['shipping'] ?? 0.0);
            $netBeforeVat = max(0.0, $subtotal - $discount);
            $tax = round($netBeforeVat * 0.15, 2);
            $total = round($netBeforeVat + $tax + $shipping, 2);

            $order = Order::updateOrCreate(
                ['order_number' => $o['order_number']],
                [
                    'invoice_number'   => $o['invoice_number'],
                    'channel'          => $o['channel'],
                    'customer_name'    => $o['customer_name'],
                    'customer_email'   => $o['customer_email'] ?? ($customer?->email),
                    'customer_phone'   => $customer?->phone ?? '+20 100 123 4567',
                    'customer_id'      => $customer?->id,
                    'date'             => $o['date'],
                    'status'           => $o['status'],
                    'payment_method'   => $o['payment_method'],
                    'payment_status'   => $o['payment_status'],
                    'payment_transaction_id' => 'WIRE-' . strtoupper(substr(md5($o['invoice_number']), 0, 8)),
                    'subtotal'         => $subtotal,
                    'discount'         => $discount,
                    'coupon_code'      => $o['coupon_code'],
                    'shipping'         => $shipping,
                    'tax'              => $tax,
                    'total'            => $total,
                    'shipping_address' => [
                        'recipient' => $o['customer_name'],
                        'street'    => $customer?->address ?? 'King Fahd Road, Al Olaya',
                        'city'      => $customer?->city ?? 'Riyadh',
                        'country'   => $customer?->country ?? 'Saudi Arabia',
                    ],
                ]
            );

            $order->items()->delete();

            foreach ($resolvedLines as $line) {
                /** @var Product $product */
                $product = $line['product'];
                $fefoBatch = ProductBatch::where('product_id', $product->id)
                    ->where('status', 'approved')
                    ->whereDate('expiry_date', '>', now()->toDateString())
                    ->orderBy('expiry_date', 'asc')
                    ->orderBy('id', 'asc')
                    ->first();

                OrderItem::create([
                    'order_id'         => $order->id,
                    'item_type'        => 'product',
                    'product_id'       => $product->id,
                    'product_batch_id' => $fefoBatch?->id,
                    'batch_number'     => $fefoBatch?->batch_number,
                    'expiry_date'      => $fefoBatch?->expiry_date,
                    'product_name_en'  => $product->name_en,
                    'product_name_ar'  => $product->name_ar,
                    'variant_en'       => $product->product_size ?? 'Standard Clinical Pack',
                    'variant_ar'       => 'العبوة السريرية القياسية',
                    'sku'              => $product->sku,
                    'unit_price'       => $line['unit_price'],
                    'quantity'         => $line['qty'],
                    'total'            => $line['line_total'],
                    'image'            => $product->image,
                ]);
            }
        }

        // Recalculate customer metrics based on real database records
        foreach (Customer::all() as $cust) {
            $custOrders = Order::where('customer_id', $cust->id)->where('status', '!=', 'cancelled');
            $cust->update([
                'total_orders' => (clone $custOrders)->count(),
                'total_spent'  => (clone $custOrders)->sum('total'),
            ]);
        }
    }

    /* ================================================================== */
    /* INVENTORY & MULTI-LOT FEFO BATCHES                                 */
    /* ================================================================== */

    private function seedInventory(): void
    {
        // Ensure active two-hub locations are enabled and legacy offline hub is disabled
        Location::updateOrCreate(['id' => 'online'], [
            'name_en' => 'Online Fulfillment Hub',
            'name_ar' => 'مستودع الطلبات الإلكترونية',
            'code' => 'LOC-ONL',
            'type' => 'online',
            'is_active' => true,
        ]);
        Location::updateOrCreate(['id' => 'central_wh'], [
            'name_en' => 'Central Logistics & Quarantine Warehouse',
            'name_ar' => 'المستودع المركزي الرئيسي',
            'code' => 'LOC-CWH',
            'type' => 'warehouse',
            'is_active' => true,
        ]);
        Location::where('id', 'offline')->update(['is_active' => false]);

        foreach (InventoryViewModel::stockItems() as $si) {
            $product = Product::where('sku', $si['sku'] ?? '')->first();
            if (!$product) {
                continue;
            }

            InventoryItem::updateOrCreate(
                ['product_id' => $product->id, 'location_id' => $si['location_id']],
                [
                    'location_name_en'    => $si['location_name_en'],
                    'location_name_ar'    => $si['location_name_ar'] ?? null,
                    'variant_en'          => $si['variant_en'] ?? null,
                    'variant_ar'          => $si['variant_ar'] ?? null,
                    'current_stock'       => $si['current_stock'] ?? 0,
                    'available_stock'     => $si['available_stock'] ?? 0,
                    'reserved_stock'      => $si['reserved_stock'] ?? 0,
                    'low_stock_threshold' => $si['low_stock_threshold'] ?? 15,
                    'status'              => $si['status'] ?? 'in_stock',
                    'unit_cost'           => $si['unit_cost'] ?? $product->cost_price,
                    'retail_price'        => $si['retail_price'] ?? $product->price,
                ]
            );
        }

        // Seed multi-lot FEFO ProductBatches (an earlier-expiry lot + a fresh primary lot per product)
        $products = Product::all();
        foreach ($products as $idx => $product) {
            $onlineStock = (int) InventoryItem::where('product_id', $product->id)->where('location_id', 'online')->value('current_stock');
            $centralStock = (int) InventoryItem::where('product_id', $product->id)->where('location_id', 'central_wh')->value('current_stock');

            // Split online stock into 2 batches so FEFO ordering & Expiry alerts show realistic multi-lot queues
            $earlyOnline = $onlineStock >= 30 ? 30 : $onlineStock;
            $freshOnline = max(0, $onlineStock - $earlyOnline);

            if ($earlyOnline > 0) {
                $earlyExpDays = 75 + ($idx * 12); // Near expiry watch window (75-111 days)
                ProductBatch::updateOrCreate(
                    ['batch_number' => sprintf('LOT-FG-202506-00%02d', $product->id)],
                    [
                        'product_id' => $product->id,
                        'location_id' => 'online',
                        'storage_bin' => 'ONL-FEFO-A' . $product->id,
                        'initial_qty' => $earlyOnline + 15,
                        'current_qty' => $earlyOnline,
                        'available_qty' => $earlyOnline,
                        'reserved_qty' => 0,
                        'quarantine_qty' => 0,
                        'unit_cost' => (float) ($product->cost_price ?? 25.00),
                        'manufacture_date' => now()->subMonths(18)->toDateString(),
                        'expiry_date' => now()->addDays($earlyExpDays)->toDateString(),
                        'status' => 'approved',
                    ]
                );
            }

            if ($freshOnline > 0) {
                ProductBatch::updateOrCreate(
                    ['batch_number' => sprintf('LOT-FG-202610-01%02d', $product->id)],
                    [
                        'product_id' => $product->id,
                        'location_id' => 'online',
                        'storage_bin' => 'ONL-RACK-B' . $product->id,
                        'initial_qty' => $freshOnline,
                        'current_qty' => $freshOnline,
                        'available_qty' => $freshOnline,
                        'reserved_qty' => 0,
                        'quarantine_qty' => 0,
                        'unit_cost' => (float) ($product->cost_price ?? 25.00),
                        'manufacture_date' => now()->subMonth()->toDateString(),
                        'expiry_date' => $product->calculateDefaultExpiryDate(now()->subMonth()),
                        'status' => 'approved',
                    ]
                );
            }

            if ($centralStock > 0) {
                ProductBatch::updateOrCreate(
                    ['batch_number' => sprintf('LOT-FG-202610-02%02d', $product->id)],
                    [
                        'product_id' => $product->id,
                        'location_id' => 'central_wh',
                        'storage_bin' => 'CWH-PALLET-' . $product->id,
                        'initial_qty' => $centralStock,
                        'current_qty' => $centralStock,
                        'available_qty' => $centralStock,
                        'reserved_qty' => 0,
                        'quarantine_qty' => 0,
                        'unit_cost' => (float) ($product->cost_price ?? 25.00),
                        'manufacture_date' => now()->subWeeks(3)->toDateString(),
                        'expiry_date' => $product->calculateDefaultExpiryDate(now()->subWeeks(3)),
                        'status' => 'approved',
                    ]
                );
            }
        }

        foreach (InventoryViewModel::movements() as $m) {
            $product = Product::where('sku', $m['sku'] ?? '')->first();

            InventoryMovement::create([
                'product_id'      => $product?->id,
                'product_name_en' => $m['product_name_en'],
                'product_name_ar' => $m['product_name_ar'] ?? null,
                'sku'             => $m['sku'] ?? null,
                'movement_type'   => $m['movement_type'],
                'from_location'   => $m['from_location'] ?? null,
                'to_location'     => $m['to_location'] ?? null,
                'quantity'        => $m['quantity'],
                'previous_qty'    => $m['previous_qty'] ?? null,
                'new_qty'         => $m['new_qty'] ?? null,
                'date'            => $m['date'],
                'time'            => $m['time'],
                'user'            => $m['user'] ?? null,
                'note'            => $m['note'] ?? null,
            ]);
        }

        try {
            InventoryService::syncAllProductsInventory();
        } catch (\Throwable $e) {
            $this->command?->warn('InventoryService sync skipped: ' . $e->getMessage());
        }
    }
}
        return [

            /* ---------------------------------------------------------- */
            /* 1. BLUE MIND                                                */
            /* ---------------------------------------------------------- */
            [
                'id'              => 1,
                'slug'            => 'blue-mind',
                'sku'             => 'BZ-MND-001',
                'barcode'         => '628100091001',
                'name_en'         => 'BLUE MIND',
                'name_ar'         => 'بلو مايند',
                'tagline_en'      => 'Daily Cognitive & Nootropic Support',
                'tagline_ar'      => 'دعم إدراكي وتركيز عصبي يومي متطور',
                'category_en'     => 'Cognitive & Brain Health',
                'subcategory_en'  => 'Nootropics',
                'subcategory_ar'  => 'منشطات الذهن الطبيعية',
                'brand'           => 'Blue Zone Bioceuticals',
                'price'           => 68.00,
                'sale_price'      => 58.00,
                'cost_price'      => 28.00,
                'is_featured'     => true,
                'is_best_seller'  => true,
                'is_new'          => false,
                'status'          => 'active',
                'rating'          => 4.9,
                'reviews_count'   => 142,
                'image'           => '/assets/products/blue-mind.jpg',
                'images'          => ['/assets/products/blue-mind.jpg'],
                'stock_online'    => 124,
                'low_stock_threshold' => 15,
                'short_description_en' => 'Precision-engineered nootropic complex supporting synaptic plasticity, memory recall, and sustained cerebral focus without jitters.',
                'short_description_ar' => 'مركب نوتروبيك عالي الدقة يدعم مرونة المشابك العصبية، سرعة الاسترجاع الذهني، والتركيز المتواصل دون توتر.',
                'description_en'  => 'Inspired by the cognitive longevity of Ikarian and Okinawan centenarians, BLUE MIND synergizes pharmaceutical-grade bio-lipids with standardized plant adaptogens. It modulates acetylcholine synthesis while shielding neuronal mitochondria from oxidative degradation.',
                'description_ar'  => 'مستوحى من طول العمر الإدراكي لدى كبار السن في إيكاريا وأوكيناوا. يجمع بلو مايند بين الدهون الحيوية ذات الجودة الصيدلانية ومضادات الأكسدة النباتية الموحدة لتحفيز الأستيل كولين وحماية الميتوكوندريا العصبية.',
                'science_en'      => "BLUE MIND is built on converging scientific evidence that the aging brain suffers most from declining acetylcholine synthesis, neuroinflammation, and mitochondrial energy failure.\n\nGinkgo Biloba (24% flavone glycosides) improves cerebral blood flow and oxygen delivery to prefrontal cortex neurons via vasodilatory effects on cerebral arterioles. Phosphatidylserine (PS) is a structural phospholipid in neuronal membranes; supplementation is shown in multiple RCTs to measurably improve memory, recall speed, and mood in aging adults.\n\nPhosphatidylcholine serves as a direct choline donor sustaining acetylcholine neurotransmitter pools. Ubiquinol CoQ10 (Kaneka) powers neuronal mitochondria, and Setria Glutathione neutralizes reactive oxygen species accumulating in synaptic gaps.",
                'science_ar'      => "يُبنى بلو مايند على أدلة علمية متقاطعة تُثبت أن الدماغ المتقدم في العمر يعاني من انخفاض تركيب الأستيل كولين، والالتهاب العصبي، وفشل الطاقة الميتوكوندريا.\n\nيعمل مستخلص الجنكة بيلوبا (24% غليكوزيدات فلافون) عبر توسيع الشرايين الدماغية الدقيقة، مما يُحسّن تدفق الدم وإيصال الأكسجين للخلايا العصبية. الفوسفاتيديل سيرين مكوّن أساسي في أغشية الخلايا العصبية وقد أثبتت التجارب المعشاة تأثيره في تحسين الذاكرة وسرعة الاسترجاع الذهني.\n\nيوفر الفوسفاتيديل كولين الكولين اللازم لتركيب الأستيل كولين العصبي، فيما يُشغّل اليوبيكوينول الميتوكوندريا العصبية ويُقلل من تراكم الجذور الحرة.",
                'benefits_en'     => [
                    'Sustained mental stamina for 8+ hours without caffeine crash',
                    'Supports neurogenesis and BDNF (Brain-Derived Neurotrophic Factor) signaling',
                    'Clinically studied dosages for verbal fluency and working memory',
                    'Shields neural micro-vessels with targeted bioflavonoids',
                ],
                'benefits_ar'     => [
                    'طاقة ذهنية متواصلة لأكثر من 8 ساعات دون هبوط أو كافيين',
                    'يدعم تجدد الخلايا العصبية وإشارات عامل التغذية العصبية BDNF',
                    'جرعات مدروسة سريرياً لتحسين الطلاقة اللغوية والذاكرة العاملة',
                    'حماية الأوعية الدموية الدقيقة في الدماغ بالبيوفلافونويد المنقى',
                ],
                'usage_en'        => 'Take 2 capsules every morning with 250ml of mineral water, preferably alongside a healthy fat source such as cold-pressed extra virgin olive oil.',
                'usage_ar'        => 'تناول كبسولتين كل صباح مع 250 مل من الماء النقي، يفضل مع مصدر دهون صحية كزيت الزيتون البكر الممتاز.',
                'gender'          => 'Unisex',
                'age_group'       => '18+',
                'package_size_en' => '60 Vegetable Capsules (30-day supply)',
                'professional_info' => [
                    'clinical_mechanism'   => 'Choline donor phosphatidylcholine combined with phospholipid-bound serine promotes synaptic vesicle docking. Ginkgo flavonoids enhance cerebral microcirculation and oxygen perfusion rate.',
                    'formula_details'      => 'Standardized Ginkgo Biloba (24% flavone glycosides, 6% terpene lactones), Phosphatidylserine 20% soy-free sunflower lecithin, Ubiquinol CoQ10 99.5% pure Kaneka bio-fermented.',
                    'contraindications'    => 'Patients taking anticoagulants (warfarin, heparin) or MAO inhibitors must consult their supervising physician prior to administration.',
                    'warnings'             => 'Keep out of reach of children. Store below 25°C. Do not exceed recommended daily threshold without clinical supervision.',
                ],
                'ingredients' => [
                    ['name_en' => 'Ginkgo Biloba Extract (50:1)',   'name_ar' => 'مستخلص الجنكة بيلوبا (50:1)',      'dose' => '120 mg'],
                    ['name_en' => 'Phosphatidylserine (Sunflower)', 'name_ar' => 'فوسفاتيديل سيرين (دوار الشمس)',   'dose' => '100 mg'],
                    ['name_en' => 'Phosphatidylcholine',            'name_ar' => 'فوسفاتيديل كولين',                'dose' => '150 mg'],
                    ['name_en' => 'Kaneka Ubiquinol (CoQ10)',       'name_ar' => 'يوبيكوينول كانيكا المنقى',        'dose' => '100 mg'],
                    ['name_en' => 'Setria L-Glutathione',           'name_ar' => 'إل-جلوتاثيون نقي',               'dose' => '50 mg'],
                ],
            ],

            /* ---------------------------------------------------------- */
            /* 2. BLUE CELL                                                */
            /* ---------------------------------------------------------- */
            [
                'id'              => 2,
                'slug'            => 'blue-cell',
                'sku'             => 'BZ-CEL-002',
                'barcode'         => '628100091002',
                'name_en'         => 'BLUE CELL',
                'name_ar'         => 'بلو سيل',
                'tagline_en'      => 'Cellular Longevity & Mitochondrial Bio-Shield',
                'tagline_ar'      => 'تجديد الخلايا والدرع الحيوي للميتوكوندريا',
                'category_en'     => 'Cellular Longevity',
                'subcategory_en'  => 'NAD+ Precursors',
                'subcategory_ar'  => 'محفزات إنزيم NAD+',
                'brand'           => 'Blue Zone Bioceuticals',
                'price'           => 74.00,
                'sale_price'      => 64.00,
                'cost_price'      => 30.00,
                'is_featured'     => true,
                'is_best_seller'  => true,
                'is_new'          => true,
                'status'          => 'active',
                'rating'          => 4.95,
                'reviews_count'   => 189,
                'image'           => '/assets/products/blue-cell.jpg',
                'images'          => ['/assets/products/blue-cell.jpg'],
                'stock_online'    => 96,
                'low_stock_threshold' => 12,
                'short_description_en' => 'Comprehensive mitochondrial revitalizer engineered with pharmaceutical NMN and trans-resveratrol to replenish cellular NAD+ pools.',
                'short_description_ar' => 'مجدد حيوي شامل للميتوكوندريا الخلوية معزز بـ NMN فائق النقاء وريسفيراترول لتحفيز مستويات NAD+ في الجسم.',
                'description_en'  => 'Formulated to target the primary hallmarks of biological aging. BLUE CELL optimizes mitochondrial energy synthesis, supports DNA repair enzyme PARP-1, and activates longevity sirtuin pathways.',
                'description_ar'  => 'مطور لاستهداف الجذور الأساسية للشيخوخة البيولوجية. يعزز إنتاج الطاقة الخلوية، ويدعم إنزيمات إصلاح الحمض النووي PARP-1، وينشط مسارات السيرتوين.',
                'science_en'      => "BLUE CELL addresses the central biochemical mechanism underlying cellular aging: the progressive depletion of NAD+. NAD+ is the essential cofactor for over 500 enzymatic reactions spanning DNA repair (via PARP enzymes), mitochondrial energy generation, and longevity signaling (via sirtuins).\n\nBeta-NMN (99.8% enzymatic-grade) is directly transported via the Slc12a8 transporter into cells where it is rapidly phosphorylated into NAD+. Clinical studies show 14-day supplementation restores NAD+ to levels typical of individuals 10-20 years younger.\n\nTrans-Resveratrol acts as an allosteric SIRT1 activator, potentiating the life-extending effects of elevated NAD+. Quercetin Phytosome and Fisetin are senolytic compounds clinically shown to selectively clear senescent cells that drive inflammatory aging.",
                'science_ar'      => "يعالج بلو سيل الآلية البيوكيميائية المحورية للشيخوخة الخلوية: الاستنزاف التدريجي لجزيء NAD+، الضروري لأكثر من 500 تفاعل إنزيمي تشمل إصلاح الحمض النووي، وتوليد الطاقة الميتوكوندريا، وتشغيل مسارات طول العمر السيرتوينية.\n\nيُنقل بيتا NMN مباشرة إلى الخلايا عبر ناقل Slc12a8 ليتحول سريعاً إلى NAD+. وتُظهر الدراسات السريرية أن 14 يوماً من التكميل ترفع مستويات NAD+ إلى مستويات من هم أصغر بـ 10-20 عاماً.\n\nيعمل ترانس ريسفيراترول كمنشط SIRT1 أليوستيري مُضاعفاً تأثيرات ارتفاع NAD+. كيرسيتين وفيسيتين مركبات سينوليتيكية تُزيل الخلايا الشائخة المتراكمة.",
                'benefits_en'     => [
                    'Elevates whole-blood NAD+ levels within 14 days of sustained intake',
                    'Potentiates mitochondrial biogenesis and cellular ATP replenishment',
                    'Protects genomic integrity against environmental oxidative stress',
                    'Promotes optimal sirtuin SIRT1 and SIRT3 enzymatic activation',
                ],
                'benefits_ar'     => [
                    'يرفع مستويات NAD+ في الدم خلال 14 يوماً من الاستخدام المنتظم',
                    'يعزز التجدد الحيوي للميتوكوندريا وإنتاج طاقة ATP الخلوية',
                    'يحمي سلامة الجينوم والحمض النووي من التأكسد البيئي',
                    'ينشط بروتينات طول العمر SIRT1 و SIRT3 بكفاءة عالية',
                ],
                'usage_en'        => 'Take 2 capsules in the morning fasting with a glass of water, or as clinically recommended.',
                'usage_ar'        => 'تناول كبسولتين صباحاً على معدة خاوية مع كوب ماء نقي.',
                'gender'          => 'Unisex',
                'age_group'       => '25+',
                'package_size_en' => '60 Enteric Veg Capsules',
                'professional_info' => [
                    'clinical_mechanism'   => 'Beta-Nicotinamide Mononucleotide is transported through the Slc12a8 carrier to rapidly phosphorylate into NAD+. Trans-resveratrol acts as an allosteric activator of SIRT1 deacetylase.',
                    'formula_details'      => 'Beta-NMN 99.8% enzymatic grade, Micronized 99% Trans-Resveratrol, Apigenin (chamomile isolate), Quercetin Phytosome.',
                    'contraindications'    => 'Contraindicated in active oncological therapy without oncologist pre-clearance. Not evaluated during pregnancy or lactation.',
                    'warnings'             => 'Keep refrigerated after opening. Do not expose to temperatures exceeding 30°C.',
                ],
                'ingredients' => [
                    ['name_en' => 'Beta-NMN (99.8% Ultra-Pure)',         'name_ar' => 'بيتا NMN فائق النقاوة (99.8%)',     'dose' => '300 mg'],
                    ['name_en' => 'Micronized Trans-Resveratrol',         'name_ar' => 'ترانس ريسفيراترول دقيق الجزيئات',  'dose' => '150 mg'],
                    ['name_en' => 'Quercetin Phytosome',                  'name_ar' => 'كيرسيتين فيتوسوم سريع الامتصاص',   'dose' => '100 mg'],
                    ['name_en' => 'Fisetin (Stem of Cotinus coggygria)',  'name_ar' => 'فيسيتين نقي من خلاصة الأشجار',     'dose' => '50 mg'],
                ],
            ],

            /* ---------------------------------------------------------- */
            /* 3. BLUE DEFENSE                                             */
            /* ---------------------------------------------------------- */
            [
                'id'              => 3,
                'slug'            => 'blue-defense',
                'sku'             => 'BZ-DEF-003',
                'barcode'         => '628100091003',
                'name_en'         => 'BLUE DEFENSE',
                'name_ar'         => 'بلو ديفينس',
                'tagline_en'      => 'Immune Resilience & Polyphenol Complex',
                'tagline_ar'      => 'مناعة فائقة ومجمع البوليفينول الطبيعي',
                'category_en'     => 'Immunity & Resilience',
                'subcategory_en'  => 'Polyphenols',
                'subcategory_ar'  => 'البوليفينول ومضادات الأكسدة',
                'brand'           => 'Blue Zone Bioceuticals',
                'price'           => 52.00,
                'sale_price'      => 45.00,
                'cost_price'      => 20.00,
                'is_featured'     => true,
                'is_best_seller'  => false,
                'is_new'          => false,
                'status'          => 'active',
                'rating'          => 4.85,
                'reviews_count'   => 97,
                'image'           => '/assets/products/blue-defense.jpg',
                'images'          => ['/assets/products/blue-defense.jpg'],
                'stock_online'    => 140,
                'low_stock_threshold' => 20,
                'short_description_en' => 'Synergistic botanical defense matrix combining high-potency elderberry, olive leaf oleuropein, zinc glycinate, and bio-fermented vitamin C.',
                'short_description_ar' => 'مركب نباتي دفاعي متكامل يجمع بين خلاصة البيلسان، مستخلص أوراق الزيتون، زنك غليسينات، وفيتامين C المخمر حيوياً.',
                'description_en'  => 'Formulated from the wild mountain botanicals of Sardinia and Greece. Strengthens upper respiratory defense barriers and supports innate immune cell chemotaxis.',
                'description_ar'  => 'مستخلص من أعشاب جبال سردينيا واليونان البرية. يعزز حواجز الجهاز التنفسي ويدعم حركة خلايا المناعة الفطرية الطبيعية.',
                'science_en'      => "BLUE DEFENSE provides multi-layered immune support drawn from ethnobotanical traditions of the Mediterranean Blue Zones — regions where centenarian immune resilience is uniquely documented.\n\nEuropean Elderberry (Sambucus nigra, 64:1) contains anthocyanidins that directly inhibit viral neuraminidase, the enzyme responsible for viral host-cell entry. Olive Leaf Extract (20% Oleuropein) activates T-cell proliferation and demonstrates antiviral activity through membrane disruption mechanisms.\n\nZinc Bisglycinate (TRAACS chelated) provides the most bioavailable zinc form — the critical co-factor for over 300 immune enzymes. Bio-fermented Ascorbic Acid from Acerola Cherry enhances natural killer (NK) cell cytotoxicity.",
                'science_ar'      => "يوفر بلو ديفينس دعماً مناعياً متعدد الطبقات مستمداً من تقاليد الطب العشبي في مناطق البلو زون المتوسطية.\n\nيحتوي خلاصة البيلسان الأوروبي (64:1) على أنثوسيانيدينات تثبّط مباشرةً إنزيم النيورامينيداز الفيروسي. يُنشط مستخلص أوراق الزيتون (20% أوليوروبين) تكاثر الخلايا التائية ويُظهر نشاطاً مضاداً للفيروسات المغلفة.\n\nيوفر زنك البيسغليسينات المخلبي (TRAACS) أعلى معدلات امتصاص للزنك — المعدن الحيوي لأكثر من 300 إنزيم مناعي. فيتامين C الطبيعي من كرز الأسيرولا يعزز نشاط خلايا القاتل الطبيعي.",
                'benefits_en'     => [
                    'Multi-tiered immune system reinforcement against seasonal challenges',
                    'Standardized 20% Oleuropein for cellular vascular defense',
                    'Chelated zinc with enhanced bioavailability without gastrointestinal discomfort',
                    'Powerful antioxidant scavenger reducing inflammatory biomarkers',
                ],
                'benefits_ar'     => [
                    'تعزيز مناعي متعدد الطبقات لمواجهة تقلبات المواسم',
                    'خلاصة أوليوروبين موحدة بنسبة 20% لحماية الأوعية والخلايا',
                    'زنك مخلبي عالي الامتصاص لطيف على المعدة بدون إزعاج هضمي',
                    'مضاد أكسدة فائق يقلل مؤشرات الإجهاد التأكسدي والالتهابي',
                ],
                'usage_en'        => 'Take 2 capsules daily with your midday meal.',
                'usage_ar'        => 'تناول كبسولتين يومياً مع وجبة الغداء.',
                'gender'          => 'Unisex',
                'age_group'       => '12+',
                'package_size_en' => '60 Vegetable Capsules',
                'professional_info' => [
                    'clinical_mechanism'   => 'Inhibits viral neuraminidase activity via anthocyanidins. Enhances T-cell proliferation and neutralizes reactive nitrogen intermediates.',
                    'formula_details'      => 'Sambucus Nigra Extract 64:1, Olea Europaea Leaf (20% Oleuropein), Zinc Bisglycinate Chelate (TRAACS), Acerola Berry Bio-Fermented Ascorbic Acid.',
                    'contraindications'    => 'Use caution in autoimmune disorders such as MS, SLE, or Rheumatoid Arthritis without immunology consultation.',
                    'warnings'             => 'Do not use as a substitute for acute antimicrobial clinical therapy.',
                ],
                'ingredients' => [
                    ['name_en' => 'European Elderberry (Sambucus nigra)',  'name_ar' => 'خلاصة البيلسان الأوروبي المركز',            'dose' => '350 mg'],
                    ['name_en' => 'Olive Leaf Extract (20% Oleuropein)',   'name_ar' => 'مستخلص ورق الزيتون (20% أوليوروبين)',        'dose' => '200 mg'],
                    ['name_en' => 'Zinc Bisglycinate Chelate',             'name_ar' => 'زنك مخلبي فائق الامتصاص',                  'dose' => '25 mg'],
                    ['name_en' => 'Acerola Cherry Bio-Vitamin C',          'name_ar' => 'فيتامين C طبيعي من كرز الأسيرولا',          'dose' => '180 mg'],
                ],
            ],

            /* ---------------------------------------------------------- */
            /* 4. BLUE METABOLIC                                           */
            /* ---------------------------------------------------------- */
            [
                'id'              => 4,
                'slug'            => 'blue-metabolic',
                'sku'             => 'BZ-MET-004',
                'barcode'         => '628100091004',
                'name_en'         => 'BLUE METABOLIC',
                'name_ar'         => 'بلو ميتابوليك',
                'tagline_en'      => 'AMPK Activator & Fasting Mimetic',
                'tagline_ar'      => 'منشط مسار AMPK ومحاكي الصيام الصحي',
                'category_en'     => 'Metabolic Health',
                'subcategory_en'  => 'Glucose Regulation',
                'subcategory_ar'  => 'تنظيم سكر الدم والأنسولين',
                'brand'           => 'Blue Zone Bioceuticals',
                'price'           => 59.00,
                'sale_price'      => 52.00,
                'cost_price'      => 24.00,
                'is_featured'     => true,
                'is_best_seller'  => false,
                'is_new'          => true,
                'status'          => 'active',
                'rating'          => 4.9,
                'reviews_count'   => 84,
                'image'           => '/assets/products/blue-metabolic.jpg',
                'images'          => ['/assets/products/blue-metabolic.jpg'],
                'stock_online'    => 80,
                'low_stock_threshold' => 10,
                'short_description_en' => 'Cellular metabolic modulator mimicking the benefits of caloric restriction and intermittent fasting through direct AMPK pathway activation.',
                'short_description_ar' => 'معدل أيض خلوي يحاكي فوائد الصيام المتقطع والتحكم الحراري عبر تنشيط مسار إنزيم AMPK المركزي.',
                'description_en'  => 'Formulated with Berberine phytosome, Ceylon cinnamon bark extract, and Chromium picolinate to optimize postprandial glucose sensitivity and hepatic lipid transport.',
                'description_ar'  => 'يحتوي على البربرين الفيتوسومي وخلاصة قرفة سيلان والكروميوم بيكولينات لدعم توازن سكر الدم بعد الوجبات وتحفيز حرق الدهون الخلوية.',
                'science_en'      => "BLUE METABOLIC is designed around the AMPK (AMP-Activated Protein Kinase) signaling axis — the master regulator of cellular energy homeostasis. Activation of AMPK mimics the physiological state of fasting, promoting fat oxidation, suppressing lipogenesis, and inducing protective autophagy.\n\nBerberine (Berbevis phospholipid complex, 9.6x greater bioavailability) phosphorylates AMPK via LKB1 kinase and stimulates GLUT4 transporter translocation to skeletal muscle membranes, dramatically improving postprandial glucose clearance.\n\nCeylon Cinnamon (10:1) provides type-A proanthocyanidins that inhibit intestinal alpha-glucosidase, reducing post-meal glycemic spikes. Chromium picolinate enhances insulin receptor phosphorylation, and R-Alpha Lipoic Acid regenerates antioxidant glutathione while improving mitochondrial glucose oxidation.",
                'science_ar'      => "صُمّم بلو ميتابوليك حول محور إشارات AMPK — المنظم الرئيسي لتوازن طاقة الخلية. يُحاكي تنشيط AMPK الحالة الفسيولوجية للصيام، محفزاً أكسدة الدهون وقمع تخليق الدهون الجديدة وتفعيل الالتهام الذاتي الحيوي.\n\nيحقق البربرين (مصفوفة فوسفوليبيدية Berbevis) توافراً حيوياً يفوق معدل البربرين العادي بـ 9.6 مرات، ويُحسّن بشكل كبير تصفية الغلوكوز بعد الوجبات.\n\nتُوفر قرفة سيلان (10:1) بروأنثوسيانيدينات تثبط ألفا-غلوكوزيداز المعوي، مُقلّلةً ارتفاع السكر بعد الوجبات. يُجدد حمض ألفا ليبويك R مستويات الجلوتاثيون ويُحسّن أكسدة الغلوكوز الميتوكوندريا.",
                'benefits_en'     => [
                    'Supports healthy fasting glucose and HbA1c in normal ranges',
                    'Activates cellular autophagy and fat oxidation pathways',
                    'Curbs sugar cravings and mid-afternoon energy crashes',
                    'Improves gut microbiome composition favoring Akkermansia muciniphila',
                ],
                'benefits_ar'     => [
                    'يدعم توازن مستويات السكر ومؤشر التراكمي ضمن الحدود الطبيعية',
                    'يحفز عملية الالتهام الذاتي الخلوي Autophagy وأكسدة الدهون',
                    'يقلل من الرغبة الشديدة في تناول السكريات وهبوط الطاقة المفاجئ',
                    'يدعم بكتيريا الأمعاء النافعة المسؤولة عن سلامة الغشاء المعوي',
                ],
                'usage_en'        => 'Take 1 capsule 15 minutes before your two largest carbohydrate-containing meals.',
                'usage_ar'        => 'تناول كبسولة واحدة قبل 15 دقيقة من أكبر وجبتين تحتويان على الكربوهيدرات.',
                'gender'          => 'Unisex',
                'age_group'       => '18+',
                'package_size_en' => '60 Gastro-Resistant Capsules',
                'professional_info' => [
                    'clinical_mechanism'   => 'Berberine phosphorylates AMPK via liver kinase B1 (LKB1), stimulating GLUT4 transporter translocation to skeletal muscle membrane.',
                    'formula_details'      => 'Berberine HCl (Berbevis phospholipid matrix, 9.6x bioavailability), Cinnamomum Verum 10:1 water extract, Chromium Picolinate.',
                    'contraindications'    => 'Concomitant use with antidiabetic sulfonylureas or insulin requires strict blood sugar titration to prevent hypoglycemia.',
                    'warnings'             => 'Discontinue use 2 weeks prior to scheduled surgical interventions.',
                ],
                'ingredients' => [
                    ['name_en' => 'Berbevis Berberine Phytosome',  'name_ar' => 'بربرين فيتوسومي عالي الامتصاص',         'dose' => '550 mg'],
                    ['name_en' => 'True Ceylon Cinnamon Extract',  'name_ar' => 'خلاصة قرفة سيلان النقية الأصلية',       'dose' => '200 mg'],
                    ['name_en' => 'Chromium Picolinate',           'name_ar' => 'كروميوم بيكولينات',                     'dose' => '200 mcg'],
                    ['name_en' => 'Alpha Lipoic Acid (R-Form)',    'name_ar' => 'حمض ألفا ليبويك (الصيغة الفعالة R)',    'dose' => '150 mg'],
                ],
            ],

            /* ---------------------------------------------------------- */
            /* 5. BLUE SLEEP                                               */
            /* ---------------------------------------------------------- */
            [
                'id'              => 5,
                'slug'            => 'blue-sleep',
                'sku'             => 'BZ-SLP-005',
                'barcode'         => '628100091005',
                'name_en'         => 'BLUE SLEEP',
                'name_ar'         => 'بلو سليب',
                'tagline_en'      => 'Deep Stage REM & Circadian Re-alignment',
                'tagline_ar'      => 'نوم عميق وإعادة ضبط الساعة البيولوجية',
                'category_en'     => 'Sleep & Circadian Restoration',
                'subcategory_en'  => 'Circadian Optimization',
                'subcategory_ar'  => 'تنظيم الإيقاع اليومي',
                'brand'           => 'Blue Zone Bioceuticals',
                'price'           => 48.00,
                'sale_price'      => 42.00,
                'cost_price'      => 18.00,
                'is_featured'     => false,
                'is_best_seller'  => true,
                'is_new'          => false,
                'status'          => 'active',
                'rating'          => 4.92,
                'reviews_count'   => 165,
                'image'           => '/assets/products/blue-sleep.jpg',
                'images'          => ['/assets/products/blue-sleep.jpg'],
                'stock_online'    => 110,
                'low_stock_threshold' => 15,
                'short_description_en' => 'Non-habit forming botanical and mineral elixir promoting delta-wave slow sleep, muscular relaxation, and nocturnal glymphatic brain detox.',
                'short_description_ar' => 'مركب نباتي ومعدني مهدئ غير مسبب للاعتياد، يعزز موجات دلتا العميقة، استرخاء العضلات، وإزالة السموم الدماغية ليلاً.',
                'description_en'  => 'Combining micro-dosed magnesium bisglycinate with L-theanine, tart cherry melatonin precursors, and chamomile apigenin for serene restorative sleep.',
                'description_ar'  => 'مزيج مدروس من مغنيسيوم بيسغليسينات مع إل-ثيانين وخلاصة الكرز الحامض الغنية بالميلاتونين الطبيعي وأبيجينين البابونج.',
                'science_en'      => "BLUE SLEEP is formulated around the neuroscience of the glymphatic system — the brain's nocturnal waste clearance mechanism that operates predominantly during deep slow-wave sleep (SWS). Disrupted SWS is linked to accelerated accumulation of amyloid-beta plaques associated with neurodegeneration.\n\nL-Theanine (Suntheanine certified, 99.5% purity) crosses the blood-brain barrier to antagonize excitatory glutamate receptors while boosting inhibitory GABA signaling, producing alpha-wave relaxation that facilitates sleep onset without sedation.\n\nMagnesium Bisglycinate (TRAACS chelated) corrects magnesium deficiency — prevalent in over 50% of adults — essential for glutamate-NMDA receptor down-regulation and melatonin synthesis. Montmorency Tart Cherry provides the only known dietary source of bioidentical phytomelatonin. Apigenin (98% pure) acts as a benzodiazepine receptor partial agonist — calming limbic hyperarousal without tolerance formation.",
                'science_ar'      => "صُمّم بلو سليب حول علم أعصاب الجهاز الغليمفاوي — آلية التنظيف الليلي للدماغ التي تعمل أساساً خلال مرحلة النوم العميق البطيء. يرتبط اضطراب هذه المرحلة بتراكم متسارع لبلاقات أميلويد بيتا المرتبطة بالتنكس العصبي.\n\nيعبر إل-ثيانين (Suntheanine، نقاوة 99.5%) حاجز الدم الدماغي ليُثبط مستقبلات الغلوتامات المثيرة ويعزز إشارات GABA المثبطة، منتجاً حالة استرخاء موجات ألفا دون إحداث نعاس.\n\nيُصحح مغنيسيوم بيسغليسينات سريعاً نقص المغنيسيوم المنتشر لدى أكثر من 50% من البالغين. كرز مونتمورنسي يُزود الجسم بالمصدر الغذائي الوحيد للميلاتونين النباتي البيوطابق. أبيجينين (98%) يعمل كناهض جزئي لمستقبلات البنزوديازيبين دون تطوير مقاومة.",
                'benefits_en'     => [
                    'Reduces sleep latency (time to fall asleep) naturally',
                    'Increases duration of Stage 3 slow-wave deep sleep and REM cycles',
                    'Wake up refreshed with zero morning grogginess or fog',
                    'Soothes muscular twitching and nocturnal tension with chelated magnesium',
                ],
                'benefits_ar'     => [
                    'يقلل الوقت المستغرق للاستغراق في النوم بشكل طبيعي وسلس',
                    'يطيل فترات النوم العميق واستعادة التوازن العصبي ومرحلة REM',
                    'استيقاظ بنشاط وصفاء ذهني دون خمول أو تشويش صباحي',
                    'يهدئ التشنجات العضلية والتوتر العصبي بمغنيسيوم مخلبي نقي',
                ],
                'usage_en'        => 'Take 2 capsules 45 minutes prior to sleep with a warm caffeine-free herbal infusion.',
                'usage_ar'        => 'تناول كبسولتين قبل النوم بـ 45 دقيقة مع كوب من منقوع الأعشاب الدافئ الخالي من الكافيين.',
                'gender'          => 'Unisex',
                'age_group'       => '18+',
                'package_size_en' => '60 Capsules (30 Nights)',
                'professional_info' => [
                    'clinical_mechanism'   => 'L-Theanine crosses blood-brain barrier to bind to glutamate receptors, boosting inhibitory GABA. Tart cherry provides bio-identical phytonutrient melatonin fractions.',
                    'formula_details'      => 'Magnesium Bisglycinate (TRAACS), Suntheanine pure L-theanine, Montmorency Tart Cherry 50:1, Apigenin 98%.',
                    'contraindications'    => 'Do not drive, operate heavy machinery, or consume concurrent CNS sedatives.',
                    'warnings'             => 'Consult clinician if pregnant or suffering from sleep apnea.',
                ],
                'ingredients' => [
                    ['name_en' => 'Magnesium Bisglycinate (TRAACS)',   'name_ar' => 'مغنيسيوم بيسغليسينات مخلبي',          'dose' => '200 mg elemental'],
                    ['name_en' => 'Suntheanine L-Theanine',            'name_ar' => 'إل-ثيانين نقي مسجل (Suntheanine)',     'dose' => '200 mg'],
                    ['name_en' => 'Montmorency Tart Cherry Extract',   'name_ar' => 'خلاصة كرز مونتمورنسي الحامض',          'dose' => '300 mg'],
                    ['name_en' => 'Chamomile Flower Apigenin',         'name_ar' => 'أبيجينين زهور البابونج المهدئ',         'dose' => '50 mg'],
                ],
            ],

            /* ---------------------------------------------------------- */
            /* 6. BLUE VITALITY                                            */
            /* ---------------------------------------------------------- */
            [
                'id'              => 6,
                'slug'            => 'blue-vitality',
                'sku'             => 'BZ-VIT-006',
                'barcode'         => '628100091006',
                'name_en'         => 'BLUE VITALITY',
                'name_ar'         => 'بلو فايتاليتي',
                'tagline_en'      => 'Cardiovascular Longevity & Endothelial Health',
                'tagline_ar'      => 'طول عمر القلب وصحة الأوعية الدموية',
                'category_en'     => 'Cardiovascular Longevity',
                'subcategory_en'  => 'Nitric Oxide & Lipids',
                'subcategory_ar'  => 'أكسيد النيتريك وتوازن الدهون',
                'brand'           => 'Blue Zone Bioceuticals',
                'price'           => 62.00,
                'sale_price'      => 54.00,
                'cost_price'      => 25.00,
                'is_featured'     => true,
                'is_best_seller'  => false,
                'is_new'          => false,
                'status'          => 'active',
                'rating'          => 4.88,
                'reviews_count'   => 112,
                'image'           => '/assets/products/blue-vitality.jpg',
                'images'          => ['/assets/products/blue-vitality.jpg'],
                'stock_online'    => 65,
                'low_stock_threshold' => 12,
                'short_description_en' => 'Advanced endothelial formula enhancing nitric oxide production, arterial compliance, and cellular microvascular flow.',
                'short_description_ar' => 'تركيبة بطانية متقدمة ترفع إنتاج أكسيد النيتريك وتعزز مرونة الشرايين والتروية الدموية الدقيقة.',
                'description_en'  => 'Formulated from French Maritime Pine Bark extract, fermented beetroot nitrates, and MenaQ7 natural vitamin K2 to promote youthful arterial elasticity.',
                'description_ar'  => 'مستخلص من لحاء الصنوبر البحري الفرنسي ونترات الشمندر المخمر مع فيتامين K2 الطبيعي لتعزيز مرونة الشرايين وصحة القلب.',
                'science_en'      => "BLUE VITALITY addresses three key pillars of cardiovascular aging: declining endothelial Nitric Oxide (NO) production, progressive arterial stiffening, and ectopic vascular calcification.\n\nPycnogenol (French Maritime Pine Bark, 65-75% procyanidins) upregulates endothelial Nitric Oxide Synthase (eNOS) gene expression, increasing vasodilatory NO while reducing vasoconstrictor endothelin-1. Clinical trials demonstrate significant reductions in blood viscosity and platelet aggregation.\n\nFermented Red Beet (10% nitrate matrix) provides dietary inorganic nitrates converted by oral bacteria to bioactive NO — a second independent NO pathway that remains active even when endothelial function is compromised. MenaQ7 (Vitamin K2 as MK-7, 180 mcg) is the only form of K2 proven to carboxylate Matrix Gla Protein (MGP), preventing calcium deposition in arterial walls.",
                'science_ar'      => "يعالج بلو فايتاليتي ثلاثة أعمدة رئيسية لشيخوخة القلب والأوعية: انخفاض إنتاج أكسيد النيتريك البطاني، وتصلب الشرايين التدريجي، والتكلس الوعائي خارج الموضع.\n\nيرفع بيكنوجينول (لحاء الصنوبر البحري، 65-75% بروسيانيدينات) تعبير جين eNOS البطاني، مُحفّزاً إنتاج أكسيد النيتريك الموسّع للأوعية مُقلّلاً الإندوثيلين-1 المضيّق. وتُثبت التجارب السريرية تخفيضاً ملحوظاً في لزوجة الدم وتجمع الصفائح الدموية.\n\nيوفر الشمندر الأحمر المخمر (10% نترات) نترات غذائية تُحوّل إلى أكسيد نيتريك فعّال — مسار NO ثانٍ مستقل عن eNOS. فيتامين K2 (MenaQ7) هو الصيغة الوحيدة المثبتة لكربوكسة بروتين MGP، مانعةً ترسب الكالسيوم في جدران الشرايين.",
                'benefits_en'     => [
                    'Stimulates endothelial Nitric Oxide Synthase (eNOS) for healthy circulation',
                    'Assists arterial flexibility and healthy blood pressure within normal range',
                    'Directs calcium away from soft arterial walls into bone matrix via Vitamin K2',
                    'Boosts physical endurance and cellular oxygen delivery',
                ],
                'benefits_ar'     => [
                    'يحفز إنزيم eNOS الطبيعي لتعزيز تدفق الدم الصحي لجميع الأعضاء',
                    'يدعم مرونة الشرايين والحفاظ على ضغط دم متوازن ومثالي',
                    'يوجه الكالسيوم نحو العظام ويبعده عن التكلس في جدران الشرايين',
                    'يرفع طاقة التحمل العضلي والتروية بالأكسجين أثناء النشاط',
                ],
                'usage_en'        => 'Take 2 capsules daily with breakfast.',
                'usage_ar'        => 'تناول كبسولتين يومياً مع وجبة الإفطار.',
                'gender'          => 'Unisex',
                'age_group'       => '25+',
                'package_size_en' => '60 Capsules',
                'professional_info' => [
                    'clinical_mechanism'   => 'Pycnogenol activates endothelial nitric oxide synthase and reduces vascular endothelin-1. MenaQ7 carboxylates Matrix Gla Protein (MGP) preventing arterial calcification.',
                    'formula_details'      => 'Standardized French Maritime Pine Bark Extract (65-75% procyanidins), Red Beetroot (10% nitrates), MenaQ7 Vitamin K2 (as MK-7) 180 mcg.',
                    'contraindications'    => 'Consult cardiologist if taking prescription nitrates or antihypertensive medications.',
                    'warnings'             => 'Do not combine with PDE-5 inhibitors without medical consent.',
                ],
                'ingredients' => [
                    ['name_en' => 'Maritime Pine Bark Extract (Pycnogenol)', 'name_ar' => 'مستخلص لحاء الصنوبر البحري الفرنسي', 'dose' => '100 mg'],
                    ['name_en' => 'Fermented Red Beet Nitrate Matrix',       'name_ar' => 'مصفوفة نترات الشمندر الأحمر المخمر', 'dose' => '400 mg'],
                    ['name_en' => 'MenaQ7 Natural Vitamin K2 (MK-7)',        'name_ar' => 'فيتامين K2 الطبيعي النقي (ميناكيو 7)','dose' => '180 mcg'],
                    ['name_en' => 'BioPerine Black Pepper Extract',          'name_ar' => 'بيوبيرين لتعزيز التوافر الحيوي',     'dose' => '5 mg'],
                ],
            ],
        ];
    }
}