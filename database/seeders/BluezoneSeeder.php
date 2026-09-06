<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\View\ViewModels\CategoryViewModel;
use App\View\ViewModels\CustomerViewModel;
use App\View\ViewModels\InventoryViewModel;
use App\View\ViewModels\OrderViewModel;
use App\View\ViewModels\RoleViewModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

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
        $this->seedOrders();
        $this->seedInventory();
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
            ['key' => 'currency',                    'value' => 'USD',                                       'group' => 'general',       'type' => 'string'],
            ['key' => 'default_currency',            'value' => 'USD',                                       'group' => 'general',       'type' => 'string'],
            ['key' => 'timezone',                    'value' => 'Asia/Riyadh',                               'group' => 'general',       'type' => 'string'],
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
        foreach ($this->getProductsData() as $p) {
            $category = Category::where('name_en', $p['category_en'] ?? '')->first();

            Product::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'slug'                 => $p['slug'],
                    'sku'                  => $p['sku'],
                    'barcode'              => $p['barcode'] ?? null,
                    'name_en'              => $p['name_en'],
                    'name_ar'              => $p['name_ar'],
                    'tagline_en'           => $p['tagline_en'] ?? null,
                    'tagline_ar'           => $p['tagline_ar'] ?? null,
                    'category_id'          => $category?->id,
                    'subcategory_en'       => $p['subcategory_en'] ?? null,
                    'subcategory_ar'       => $p['subcategory_ar'] ?? null,
                    'brand'                => $p['brand'] ?? 'Blue Zone Bioceuticals',
                    'price'                => $p['price'],
                    'sale_price'           => $p['sale_price'] ?? null,
                    'cost_price'           => $p['cost_price'] ?? null,
                    'is_featured'          => $p['is_featured'] ?? false,
                    'is_best_seller'       => $p['is_best_seller'] ?? false,
                    'is_new'               => $p['is_new'] ?? false,
                    'is_active'            => true,
                    'status'               => $p['status'] ?? 'active',
                    'rating'               => $p['rating'] ?? 0,
                    'reviews_count'        => $p['reviews_count'] ?? 0,
                    'image'                => $p['image'] ?? null,
                    'images'               => $p['images'] ?? null,
                    'stock_online'         => $p['stock_online'] ?? 0,
                    'stock_offline'        => $p['stock_offline'] ?? 0,
                    'low_stock_threshold'  => $p['low_stock_threshold'] ?? 15,
                    'short_description_en' => $p['short_description_en'] ?? null,
                    'short_description_ar' => $p['short_description_ar'] ?? null,
                    'description_en'       => $p['description_en'] ?? null,
                    'description_ar'       => $p['description_ar'] ?? null,
                    'usage_en'             => $p['usage_en'] ?? null,
                    'usage_ar'             => $p['usage_ar'] ?? null,
                    'science_en'           => $p['science_en'] ?? null,
                    'science_ar'           => $p['science_ar'] ?? null,
                    'benefits_en'          => $p['benefits_en'] ?? null,
                    'benefits_ar'          => $p['benefits_ar'] ?? null,
                    'ingredients'          => $p['ingredients'] ?? null,
                    'clinical_mechanism'   => $p['professional_info']['clinical_mechanism'] ?? null,
                    'formula_details'      => $p['professional_info']['formula_details'] ?? null,
                    'contraindications'    => $p['professional_info']['contraindications'] ?? null,
                    'warnings'             => $p['professional_info']['warnings'] ?? null,
                    'target_gender'        => $p['gender'] ?? 'Unisex',
                    'age_group'            => $p['age_group'] ?? '18+',
                    'product_size'         => $p['package_size_en'] ?? null,
                    'sort_order'           => $p['id'] ?? 0,
                ]
            );
        }
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
            // Order 1: Recent Online (Delivered)
            [
                'order_number' => 'BZ-10492',
                'invoice_number' => 'INV-2026-0841',
                'channel' => 'online',
                'customer_email' => 'zaid.harbi@example.com',
                'customer_name' => 'Dr. Zaid Al-Harbi',
                'date' => '2026-09-04',
                'status' => 'delivered',
                'payment_method' => 'Credit Card (Stripe)',
                'payment_status' => 'Paid',
                'subtotal' => 142.00,
                'discount' => 15.00,
                'coupon_code' => 'WELCOME15',
                'shipping' => 0.00,
                'tax' => 19.05,
                'total' => 146.05,
                'items' => [
                    ['sku' => 'BZ-MND-001', 'qty' => 1, 'unit_price' => 68.00],
                    ['sku' => 'BZ-CEL-002', 'qty' => 1, 'unit_price' => 74.00],
                ],
            ],
            // Order 2: Recent Online (Processing)
            [
                'order_number' => 'BZ-10491',
                'invoice_number' => 'INV-2026-0840',
                'channel' => 'online',
                'customer_email' => 'sarah.m@example.com',
                'customer_name' => 'Sarah Al-Mansoor',
                'date' => '2026-09-05',
                'status' => 'processing',
                'payment_method' => 'Apple Pay',
                'payment_status' => 'Paid',
                'subtotal' => 205.00,
                'discount' => 0.00,
                'coupon_code' => null,
                'shipping' => 0.00,
                'tax' => 30.75,
                'total' => 235.75,
                'items' => [
                    ['sku' => 'BZ-CEL-002', 'qty' => 2, 'unit_price' => 74.00],
                    ['sku' => 'BZ-MET-004', 'qty' => 1, 'unit_price' => 57.00],
                ],
            ],
            // Order 3: Recent POS Sale (Paid/Delivered)
            [
                'order_number' => 'POS-4081',
                'invoice_number' => 'INV-POS-4081',
                'channel' => 'offline',
                'customer_email' => 'tariq.otaibi@example.com',
                'customer_name' => 'Eng. Tariq Al-Otaibi',
                'date' => '2026-09-06',
                'status' => 'delivered',
                'payment_method' => 'POS Terminal (Mada)',
                'payment_status' => 'Paid',
                'subtotal' => 106.00,
                'discount' => 0.00,
                'coupon_code' => null,
                'shipping' => 0.00,
                'tax' => 15.90,
                'total' => 121.90,
                'items' => [
                    ['sku' => 'BZ-DEF-003', 'qty' => 1, 'unit_price' => 52.00],
                    ['sku' => 'BZ-VIT-006', 'qty' => 1, 'unit_price' => 54.00],
                ],
            ],
            // Order 4: POS Sale (Paid/Delivered)
            [
                'order_number' => 'POS-4080',
                'invoice_number' => 'INV-POS-4080',
                'channel' => 'offline',
                'customer_email' => 'nora.sudairy@example.com',
                'customer_name' => 'Dr. Nora Al-Sudairy',
                'date' => '2026-09-03',
                'status' => 'delivered',
                'payment_method' => 'POS Terminal (Mada)',
                'payment_status' => 'Paid',
                'subtotal' => 190.00,
                'discount' => 19.00,
                'coupon_code' => 'VIP10',
                'shipping' => 0.00,
                'tax' => 25.65,
                'total' => 196.65,
                'items' => [
                    ['sku' => 'BZ-MND-001', 'qty' => 1, 'unit_price' => 68.00],
                    ['sku' => 'BZ-CEL-002', 'qty' => 1, 'unit_price' => 74.00],
                    ['sku' => 'BZ-SLP-005', 'qty' => 1, 'unit_price' => 48.00],
                ],
            ],
            // Order 5: Online Order (Shipped)
            [
                'order_number' => 'BZ-10488',
                'invoice_number' => 'INV-2026-0835',
                'channel' => 'online',
                'customer_email' => 'faisal.ghamdi@example.com',
                'customer_name' => 'Faisal Al-Ghamdi',
                'date' => '2026-08-28',
                'status' => 'shipped',
                'payment_method' => 'Credit Card (Stripe)',
                'payment_status' => 'Paid',
                'subtotal' => 116.00,
                'discount' => 0.00,
                'coupon_code' => null,
                'shipping' => 15.00,
                'tax' => 17.40,
                'total' => 148.40,
                'items' => [
                    ['sku' => 'BZ-MND-001', 'qty' => 1, 'unit_price' => 68.00],
                    ['sku' => 'BZ-SLP-005', 'qty' => 1, 'unit_price' => 48.00],
                ],
            ],
            // Order 6: Repeat Customer Online (Delivered)
            [
                'order_number' => 'BZ-10480',
                'invoice_number' => 'INV-2026-0820',
                'channel' => 'online',
                'customer_email' => 'zaid.harbi@example.com',
                'customer_name' => 'Dr. Zaid Al-Harbi',
                'date' => '2026-08-15',
                'status' => 'delivered',
                'payment_method' => 'Credit Card (Stripe)',
                'payment_status' => 'Paid',
                'subtotal' => 222.00,
                'discount' => 20.00,
                'coupon_code' => 'LOYAL20',
                'shipping' => 0.00,
                'tax' => 30.30,
                'total' => 232.30,
                'items' => [
                    ['sku' => 'BZ-CEL-002', 'qty' => 3, 'unit_price' => 74.00],
                ],
            ],
            // Order 7: Online (Delivered)
            [
                'order_number' => 'BZ-10475',
                'invoice_number' => 'INV-2026-0810',
                'channel' => 'online',
                'customer_email' => 'lama.h@example.com',
                'customer_name' => 'Lama Al-Husseini',
                'date' => '2026-08-08',
                'status' => 'delivered',
                'payment_method' => 'Apple Pay',
                'payment_status' => 'Paid',
                'subtotal' => 109.00,
                'discount' => 0.00,
                'coupon_code' => null,
                'shipping' => 0.00,
                'tax' => 16.35,
                'total' => 125.35,
                'items' => [
                    ['sku' => 'BZ-MET-004', 'qty' => 1, 'unit_price' => 57.00],
                    ['sku' => 'BZ-DEF-003', 'qty' => 1, 'unit_price' => 52.00],
                ],
            ],
            // Order 8: POS Sale (Delivered/Paid)
            [
                'order_number' => 'POS-4072',
                'invoice_number' => 'INV-POS-4072',
                'channel' => 'offline',
                'customer_email' => 'khalid.d@example.com',
                'customer_name' => 'Khalid Al-Dossary',
                'date' => '2026-08-02',
                'status' => 'delivered',
                'payment_method' => 'Cash',
                'payment_status' => 'Paid',
                'subtotal' => 122.00,
                'discount' => 0.00,
                'coupon_code' => null,
                'shipping' => 0.00,
                'tax' => 18.30,
                'total' => 140.30,
                'items' => [
                    ['sku' => 'BZ-MND-001', 'qty' => 1, 'unit_price' => 68.00],
                    ['sku' => 'BZ-VIT-006', 'qty' => 1, 'unit_price' => 54.00],
                ],
            ],
            // Order 9: July Online Order (Delivered)
            [
                'order_number' => 'BZ-10450',
                'invoice_number' => 'INV-2026-0750',
                'channel' => 'online',
                'customer_email' => 'rayan.amri@example.com',
                'customer_name' => 'Dr. Rayan Al-Amri',
                'date' => '2026-07-24',
                'status' => 'delivered',
                'payment_method' => 'Credit Card (Stripe)',
                'payment_status' => 'Paid',
                'subtotal' => 310.00,
                'discount' => 35.00,
                'coupon_code' => 'SUMMER35',
                'shipping' => 0.00,
                'tax' => 41.25,
                'total' => 316.25,
                'items' => [
                    ['sku' => 'BZ-MND-001', 'qty' => 2, 'unit_price' => 68.00],
                    ['sku' => 'BZ-CEL-002', 'qty' => 2, 'unit_price' => 74.00],
                    ['sku' => 'BZ-SLP-005', 'qty' => 1, 'unit_price' => 48.00],
                ],
            ],
            // Order 10: July POS Boutique Sale
            [
                'order_number' => 'POS-4060',
                'invoice_number' => 'INV-POS-4060',
                'channel' => 'offline',
                'customer_email' => 'sarah.m@example.com',
                'customer_name' => 'Sarah Al-Mansoor',
                'date' => '2026-07-15',
                'status' => 'delivered',
                'payment_method' => 'POS Terminal (Mada)',
                'payment_status' => 'Paid',
                'subtotal' => 153.00,
                'discount' => 15.00,
                'coupon_code' => null,
                'shipping' => 0.00,
                'tax' => 20.70,
                'total' => 158.70,
                'items' => [
                    ['sku' => 'BZ-DEF-003', 'qty' => 1, 'unit_price' => 52.00],
                    ['sku' => 'BZ-SLP-005', 'qty' => 1, 'unit_price' => 48.00],
                    ['sku' => 'BZ-VIT-006', 'qty' => 1, 'unit_price' => 54.00],
                ],
            ],
            // Order 11: June Online Order
            [
                'order_number' => 'BZ-10410',
                'invoice_number' => 'INV-2026-0610',
                'channel' => 'online',
                'customer_email' => 'nora.sudairy@example.com',
                'customer_name' => 'Dr. Nora Al-Sudairy',
                'date' => '2026-06-28',
                'status' => 'delivered',
                'payment_method' => 'Apple Pay',
                'payment_status' => 'Paid',
                'subtotal' => 216.00,
                'discount' => 0.00,
                'coupon_code' => null,
                'shipping' => 0.00,
                'tax' => 32.40,
                'total' => 248.40,
                'items' => [
                    ['sku' => 'BZ-CEL-002', 'qty' => 2, 'unit_price' => 74.00],
                    ['sku' => 'BZ-MND-001', 'qty' => 1, 'unit_price' => 68.00],
                ],
            ],
            // Order 12: June POS Boutique
            [
                'order_number' => 'POS-4045',
                'invoice_number' => 'INV-POS-4045',
                'channel' => 'offline',
                'customer_email' => 'tariq.otaibi@example.com',
                'customer_name' => 'Eng. Tariq Al-Otaibi',
                'date' => '2026-06-12',
                'status' => 'delivered',
                'payment_method' => 'POS Terminal (Mada)',
                'payment_status' => 'Paid',
                'subtotal' => 180.00,
                'discount' => 18.00,
                'coupon_code' => null,
                'shipping' => 0.00,
                'tax' => 24.30,
                'total' => 186.30,
                'items' => [
                    ['sku' => 'BZ-MND-001', 'qty' => 1, 'unit_price' => 68.00],
                    ['sku' => 'BZ-MET-004', 'qty' => 1, 'unit_price' => 57.00],
                    ['sku' => 'BZ-VIT-006', 'qty' => 1, 'unit_price' => 54.00],
                ],
            ],
            // Order 13: May Online
            [
                'order_number' => 'BZ-10380',
                'invoice_number' => 'INV-2026-0580',
                'channel' => 'online',
                'customer_email' => 'faisal.ghamdi@example.com',
                'customer_name' => 'Faisal Al-Ghamdi',
                'date' => '2026-05-22',
                'status' => 'delivered',
                'payment_method' => 'Credit Card (Stripe)',
                'payment_status' => 'Paid',
                'subtotal' => 268.00,
                'discount' => 25.00,
                'coupon_code' => 'MAYPROMO',
                'shipping' => 0.00,
                'tax' => 36.45,
                'total' => 279.45,
                'items' => [
                    ['sku' => 'BZ-CEL-002', 'qty' => 2, 'unit_price' => 74.00],
                    ['sku' => 'BZ-MND-001', 'qty' => 1, 'unit_price' => 68.00],
                    ['sku' => 'BZ-VIT-006', 'qty' => 1, 'unit_price' => 54.00],
                ],
            ],
            // Order 14: May POS
            [
                'order_number' => 'POS-4030',
                'invoice_number' => 'INV-POS-4030',
                'channel' => 'offline',
                'customer_email' => 'lama.h@example.com',
                'customer_name' => 'Lama Al-Husseini',
                'date' => '2026-05-10',
                'status' => 'delivered',
                'payment_method' => 'POS Terminal (Mada)',
                'payment_status' => 'Paid',
                'subtotal' => 110.00,
                'discount' => 0.00,
                'coupon_code' => null,
                'shipping' => 0.00,
                'tax' => 16.50,
                'total' => 126.50,
                'items' => [
                    ['sku' => 'BZ-DEF-003', 'qty' => 1, 'unit_price' => 52.00],
                    ['sku' => 'BZ-MET-004', 'qty' => 1, 'unit_price' => 57.00],
                ],
            ],
            // Order 15: April Online
            [
                'order_number' => 'BZ-10320',
                'invoice_number' => 'INV-2026-0420',
                'channel' => 'online',
                'customer_email' => 'zaid.harbi@example.com',
                'customer_name' => 'Dr. Zaid Al-Harbi',
                'date' => '2026-04-18',
                'status' => 'delivered',
                'payment_method' => 'Credit Card (Stripe)',
                'payment_status' => 'Paid',
                'subtotal' => 196.00,
                'discount' => 20.00,
                'coupon_code' => 'LONGEVITY20',
                'shipping' => 0.00,
                'tax' => 26.40,
                'total' => 202.40,
                'items' => [
                    ['sku' => 'BZ-CEL-002', 'qty' => 2, 'unit_price' => 74.00],
                    ['sku' => 'BZ-SLP-005', 'qty' => 1, 'unit_price' => 48.00],
                ],
            ],
            // Order 16: April POS
            [
                'order_number' => 'POS-4015',
                'invoice_number' => 'INV-POS-4015',
                'channel' => 'offline',
                'customer_email' => 'khalid.d@example.com',
                'customer_name' => 'Khalid Al-Dossary',
                'date' => '2026-04-05',
                'status' => 'delivered',
                'payment_method' => 'Cash',
                'payment_status' => 'Paid',
                'subtotal' => 122.00,
                'discount' => 10.00,
                'coupon_code' => null,
                'shipping' => 0.00,
                'tax' => 16.80,
                'total' => 128.80,
                'items' => [
                    ['sku' => 'BZ-MND-001', 'qty' => 1, 'unit_price' => 68.00],
                    ['sku' => 'BZ-VIT-006', 'qty' => 1, 'unit_price' => 54.00],
                ],
            ],
            // Order 17: March Online
            [
                'order_number' => 'BZ-10250',
                'invoice_number' => 'INV-2026-0350',
                'channel' => 'online',
                'customer_email' => 'sarah.m@example.com',
                'customer_name' => 'Sarah Al-Mansoor',
                'date' => '2026-03-22',
                'status' => 'delivered',
                'payment_method' => 'Apple Pay',
                'payment_status' => 'Paid',
                'subtotal' => 270.00,
                'discount' => 25.00,
                'coupon_code' => 'MARCH25',
                'shipping' => 0.00,
                'tax' => 36.75,
                'total' => 281.75,
                'items' => [
                    ['sku' => 'BZ-CEL-002', 'qty' => 2, 'unit_price' => 74.00],
                    ['sku' => 'BZ-MND-001', 'qty' => 1, 'unit_price' => 68.00],
                    ['sku' => 'BZ-DEF-003', 'qty' => 1, 'unit_price' => 52.00],
                ],
            ],
            // Order 18: March POS
            [
                'order_number' => 'POS-4008',
                'invoice_number' => 'INV-POS-4008',
                'channel' => 'offline',
                'customer_email' => 'rayan.amri@example.com',
                'customer_name' => 'Dr. Rayan Al-Amri',
                'date' => '2026-03-10',
                'status' => 'delivered',
                'payment_method' => 'POS Terminal (Mada)',
                'payment_status' => 'Paid',
                'subtotal' => 180.00,
                'discount' => 0.00,
                'coupon_code' => null,
                'shipping' => 0.00,
                'tax' => 27.00,
                'total' => 207.00,
                'items' => [
                    ['sku' => 'BZ-MND-001', 'qty' => 1, 'unit_price' => 68.00],
                    ['sku' => 'BZ-MET-004', 'qty' => 1, 'unit_price' => 57.00],
                    ['sku' => 'BZ-VIT-006', 'qty' => 1, 'unit_price' => 54.00],
                ],
            ],
            // Order 19: February Online
            [
                'order_number' => 'BZ-10180',
                'invoice_number' => 'INV-2026-0280',
                'channel' => 'online',
                'customer_email' => 'tariq.otaibi@example.com',
                'customer_name' => 'Eng. Tariq Al-Otaibi',
                'date' => '2026-02-18',
                'status' => 'delivered',
                'payment_method' => 'Credit Card (Stripe)',
                'payment_status' => 'Paid',
                'subtotal' => 174.00,
                'discount' => 15.00,
                'coupon_code' => 'FEB15',
                'shipping' => 0.00,
                'tax' => 23.85,
                'total' => 182.85,
                'items' => [
                    ['sku' => 'BZ-CEL-002', 'qty' => 1, 'unit_price' => 74.00],
                    ['sku' => 'BZ-SLP-005', 'qty' => 1, 'unit_price' => 48.00],
                    ['sku' => 'BZ-DEF-003', 'qty' => 1, 'unit_price' => 52.00],
                ],
            ],
            // Order 20: January Online
            [
                'order_number' => 'BZ-10100',
                'invoice_number' => 'INV-2026-0100',
                'channel' => 'online',
                'customer_email' => 'zaid.harbi@example.com',
                'customer_name' => 'Dr. Zaid Al-Harbi',
                'date' => '2026-01-20',
                'status' => 'delivered',
                'payment_method' => 'Credit Card (Stripe)',
                'payment_status' => 'Paid',
                'subtotal' => 216.00,
                'discount' => 20.00,
                'coupon_code' => 'LAUNCH20',
                'shipping' => 0.00,
                'tax' => 29.40,
                'total' => 225.40,
                'items' => [
                    ['sku' => 'BZ-CEL-002', 'qty' => 2, 'unit_price' => 74.00],
                    ['sku' => 'BZ-MND-001', 'qty' => 1, 'unit_price' => 68.00],
                ],
            ],
        ];

        foreach ($orders as $o) {
            $customer = Customer::where('email', $o['customer_email'] ?? '')->first();

            $order = Order::updateOrCreate(
                ['order_number' => $o['order_number']],
                [
                    'invoice_number'   => $o['invoice_number'],
                    'channel'          => $o['channel'],
                    'customer_name'    => $o['customer_name'],
                    'customer_email'   => $o['customer_email'] ?? ($customer?->email),
                    'customer_phone'   => $customer?->phone ?? '+966 50 123 4567',
                    'customer_id'      => $customer?->id,
                    'date'             => $o['date'],
                    'status'           => $o['status'],
                    'payment_method'   => $o['payment_method'],
                    'payment_status'   => $o['payment_status'],
                    'subtotal'         => $o['subtotal'],
                    'discount'         => $o['discount'],
                    'coupon_code'      => $o['coupon_code'],
                    'shipping'         => $o['shipping'],
                    'tax'              => $o['tax'],
                    'total'            => $o['total'],
                    'shipping_address' => [
                        'recipient' => $o['customer_name'],
                        'street'    => $customer?->address ?? 'King Fahd Road',
                        'city'      => $customer?->city ?? 'Riyadh',
                        'country'   => 'Saudi Arabia',
                    ],
                ]
            );

            // Clean existing items if any
            $order->items()->delete();

            foreach ($o['items'] as $itemData) {
                $product = Product::where('sku', $itemData['sku'])->first();
                if (! $product) {
                    continue;
                }

                $qty = $itemData['qty'];
                $unitPrice = $itemData['unit_price'];

                OrderItem::create([
                    'order_id'        => $order->id,
                    'product_id'      => $product->id,
                    'product_name_en' => $product->name_en,
                    'product_name_ar' => $product->name_ar,
                    'variant_en'      => 'Standard 30-Day Protocol (60 Capsules)',
                    'variant_ar'      => 'بروتوكول 30 يوماً القياسي (60 كبسولة)',
                    'sku'             => $product->sku,
                    'unit_price'      => $unitPrice,
                    'quantity'        => $qty,
                    'total'           => $unitPrice * $qty,
                    'image'           => $product->image,
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
    /* INVENTORY                                                            */
    /* ================================================================== */

    private function seedInventory(): void
    {
        foreach (InventoryViewModel::stockItems() as $si) {
            $product = Product::where('sku', $si['sku'] ?? '')->first();

            InventoryItem::updateOrCreate(
                ['product_id' => $product?->id ?? 1, 'location_id' => $si['location_id']],
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
                    'unit_cost'           => $si['unit_cost'] ?? null,
                    'retail_price'        => $si['retail_price'] ?? null,
                ]
            );
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
            \App\Services\InventoryService::syncAllProductsInventory();
        } catch (\Throwable $e) {
            $this->command->warn('InventoryService sync skipped: ' . $e->getMessage());
        }
    }

    /* ================================================================== */
    /* PRODUCT DEFINITIONS                                                  */
    /* ================================================================== */

    private function getProductsData(): array
    {
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
                'stock_offline'   => 38,
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
                'stock_offline'   => 42,
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
                'stock_offline'   => 55,
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
                'stock_offline'   => 25,
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
                'stock_offline'   => 45,
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
                'stock_offline'   => 20,
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