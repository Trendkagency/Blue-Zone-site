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
            ['name' => 'Ziad Mohamed',   'email' => 'admin@bluezone.com',    'password' => '6pdysPuekJng', 'role_id' => $adminRole?->id],
            ['name' => 'Super Admin',    'email' => 'admin@admin.com',       'password' => 'wL8cSK62pqbZ', 'role_id' => $adminRole?->id],
            ['name' => 'Mohamed ElKholy','email' => 'm.elkholy@bluezone.com', 'password' => 'MaafKUwvUqN4', 'role_id' => $managerRole?->id],
            ['name' => 'Khalid Nassir',  'email' => 'khalid@bluezone.com',   'password' => 'KJfTbAhq7Lzi', 'role_id' => $salesRole?->id],
            ['name' => 'Sara Almutairi', 'email' => 'sara@bluezone.com',     'password' => 'bM8hYtPjBBqH', 'role_id' => $salesRole?->id],
            ['name' => 'Omar Fadhel',    'email' => 'omar@bluezone.com',     'password' => 'x2CEpbyigqp7', 'role_id' => $inventoryRole?->id],
            ['name' => 'Lina Al-Rashid', 'email' => 'lina@bluezone.com',     'password' => 'Wqy6fmeKPWim', 'role_id' => $managerRole?->id],
        ];

        foreach ($users as $u) {
            $rawPass = $u['password'] ?? 'password';
            unset($u['password']);
            User::updateOrCreate(
                ['email' => $u['email']],
                array_merge($u, [
                    'password'          => bcrypt($rawPass),
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
