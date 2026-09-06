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
use App\Models\User;
use App\View\ViewModels\CategoryViewModel;
use App\View\ViewModels\CustomerViewModel;
use App\View\ViewModels\InventoryViewModel;
use App\View\ViewModels\OrderViewModel;
use App\View\ViewModels\ProductViewModel;
use App\View\ViewModels\RoleViewModel;
use Illuminate\Database\Seeder;

class BluezoneSeeder extends Seeder
{
    /**
     * Seed all Blue Zone data from existing ViewModels into the database.
     */
    public function run(): void
    {
        $this->seedCategories();
        $this->seedProducts();
        $this->seedCustomers();
        $this->seedOrders();
        $this->seedInventory();
        $this->seedRoles();
        $this->seedAdminUser();
    }

    private function seedCategories(): void
    {
        foreach (CategoryViewModel::all() as $cat) {
            Category::create([
                'name_en' => $cat['name_en'],
                'name_ar' => $cat['name_ar'],
                'slug' => $cat['slug'],
                'icon' => $cat['icon'] ?? null,
                'description_en' => $cat['description_en'] ?? null,
                'description_ar' => $cat['description_ar'] ?? null,
                'sort_order' => $cat['sort_order'] ?? 0,
                'is_active' => ($cat['status'] ?? 'active') === 'active',
            ]);
        }
    }

    private function seedProducts(): void
    {
        foreach (ProductViewModel::all() as $p) {
            // Find category by name
            $category = Category::where('name_en', $p['category_en'] ?? '')->first();

            Product::create([
                'slug' => $p['slug'],
                'sku' => $p['sku'],
                'barcode' => $p['barcode'] ?? null,
                'name_en' => $p['name_en'],
                'name_ar' => $p['name_ar'],
                'tagline_en' => $p['tagline_en'] ?? null,
                'tagline_ar' => $p['tagline_ar'] ?? null,
                'category_id' => $category?->id,
                'subcategory_en' => $p['subcategory_en'] ?? null,
                'subcategory_ar' => $p['subcategory_ar'] ?? null,
                'brand' => $p['brand'] ?? 'Blue Zone Bioceuticals',
                'price' => $p['price'],
                'sale_price' => $p['sale_price'] ?? null,
                'cost_price' => $p['cost_price'] ?? null,
                'is_featured' => $p['is_featured'] ?? false,
                'is_best_seller' => $p['is_best_seller'] ?? false,
                'is_new' => $p['is_new'] ?? false,
                'status' => $p['status'] ?? 'active',
                'rating' => $p['rating'] ?? 0,
                'reviews_count' => $p['reviews_count'] ?? 0,
                'image' => $p['image'] ?? null,
                'images' => $p['images'] ?? null,
                'stock_online' => $p['stock_online'] ?? 0,
                'stock_offline' => $p['stock_offline'] ?? 0,
                'low_stock_threshold' => $p['low_stock_threshold'] ?? 15,
                'short_description_en' => $p['short_description_en'] ?? null,
                'short_description_ar' => $p['short_description_ar'] ?? null,
                'description_en' => $p['description_en'] ?? null,
                'description_ar' => $p['description_ar'] ?? null,
                'usage_en' => $p['usage_en'] ?? null,
                'usage_ar' => $p['usage_ar'] ?? null,
                'science_en' => $p['science_en'] ?? null,
                'science_ar' => $p['science_ar'] ?? null,
                'benefits_en' => $p['benefits_en'] ?? null,
                'benefits_ar' => $p['benefits_ar'] ?? null,
                'ingredients' => $p['ingredients'] ?? null,
                'target_gender' => $p['target_gender'] ?? 'Unisex',
                'age_group' => $p['age_group'] ?? '18+',
                'product_size' => $p['product_size'] ?? null,
            ]);
        }
    }

    private function seedCustomers(): void
    {
        foreach (CustomerViewModel::all() as $c) {
            Customer::create([
                'name' => $c['name'],
                'email' => $c['email'],
<<<<<<< HEAD
                'phone' => $c['phone'] ?? null,
                'city' => $c['city'] ?? null,
                'country' => $c['country'] ?? null,
                'total_orders' => $c['orders_count'] ?? 0,
                'total_spent' => $c['total_spent'] ?? 0,
                'status' => $c['status'] ?? 'active',
=======
                'password' => bcrypt('password'),
                'phone' => $c['phone'] ?? '+966 50 123 4567',
                'address' => '742 Longevity Way, King Fahd District',
                'city' => $c['city'] ?? 'Riyadh',
                'country' => $c['country'] ?? 'Saudi Arabia',
                'postal_code' => '12271',
                'total_orders' => $c['orders_count'] ?? 0,
                'total_spent' => $c['total_spent'] ?? 0,
                'status' => $c['status'] ?? 'active',
                'email_verified_at' => now(),
>>>>>>> origin/main
                'registered_at' => $c['registered_at'] ?? now(),
            ]);
        }
    }

    private function seedOrders(): void
    {
        foreach (OrderViewModel::all() as $o) {
            $customer = Customer::where('name', $o['customer_name'])->first();

            $order = Order::create([
                'order_number' => $o['order_number'],
                'invoice_number' => $o['invoice_number'] ?? null,
                'channel' => $o['channel'] ?? 'online',
                'customer_name' => $o['customer_name'],
                'customer_email' => $o['customer_email'] ?? null,
                'customer_phone' => $o['customer_phone'] ?? null,
                'customer_id' => $customer?->id,
                'date' => $o['date'],
                'status' => $o['status'] ?? 'Pending',
                'payment_method' => $o['payment_method'] ?? null,
                'payment_status' => $o['payment_status'] ?? 'Pending',
                'subtotal' => $o['subtotal'] ?? 0,
                'discount' => $o['discount'] ?? 0,
                'coupon_code' => $o['coupon_code'] ?? null,
                'shipping' => $o['shipping'] ?? 0,
                'tax' => $o['tax'] ?? 0,
                'total' => $o['total'] ?? 0,
                'shipping_address' => $o['shipping_address'] ?? null,
            ]);

            // Seed order items
            foreach ($o['items'] ?? [] as $item) {
                $product = Product::where('sku', $item['sku'] ?? '')->first();

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product?->id,
                    'product_name_en' => $item['product_name_en'],
                    'product_name_ar' => $item['product_name_ar'] ?? null,
                    'variant_en' => $item['variant_en'] ?? null,
                    'variant_ar' => $item['variant_ar'] ?? null,
                    'sku' => $item['sku'] ?? null,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'] ?? 1,
                    'total' => $item['total'] ?? $item['unit_price'],
                    'image' => $item['image'] ?? null,
                ]);
            }
        }
    }

    private function seedInventory(): void
    {
        // Seed stock items
        foreach (InventoryViewModel::stockItems() as $si) {
            $product = Product::where('sku', $si['sku'] ?? '')->first();

            InventoryItem::create([
                'product_id' => $product?->id ?? 1,
                'location_id' => $si['location_id'],
                'location_name_en' => $si['location_name_en'],
                'location_name_ar' => $si['location_name_ar'] ?? null,
                'variant_en' => $si['variant_en'] ?? null,
                'variant_ar' => $si['variant_ar'] ?? null,
                'current_stock' => $si['current_stock'] ?? 0,
                'available_stock' => $si['available_stock'] ?? 0,
                'reserved_stock' => $si['reserved_stock'] ?? 0,
                'low_stock_threshold' => $si['low_stock_threshold'] ?? 15,
                'status' => $si['status'] ?? 'in_stock',
                'unit_cost' => $si['unit_cost'] ?? null,
                'retail_price' => $si['retail_price'] ?? null,
            ]);
        }

        // Seed movements
        foreach (InventoryViewModel::movements() as $m) {
            $product = Product::where('sku', $m['sku'] ?? '')->first();

            InventoryMovement::create([
                'product_id' => $product?->id,
                'product_name_en' => $m['product_name_en'],
                'product_name_ar' => $m['product_name_ar'] ?? null,
                'sku' => $m['sku'] ?? null,
                'movement_type' => $m['movement_type'],
                'from_location' => $m['from_location'] ?? null,
                'to_location' => $m['to_location'] ?? null,
                'quantity' => $m['quantity'],
                'previous_qty' => $m['previous_qty'] ?? null,
                'new_qty' => $m['new_qty'] ?? null,
                'date' => $m['date'],
                'time' => $m['time'],
                'user' => $m['user'] ?? null,
                'note' => $m['note'] ?? null,
            ]);
        }
<<<<<<< HEAD
=======

        // Ensure all products have complete inventory items for all locations
        \App\Services\InventoryService::syncAllProductsInventory();
>>>>>>> origin/main
    }

    private function seedRoles(): void
    {
        $roles = RoleViewModel::all();
        foreach ($roles as $r) {
            Role::create([
                'name' => $r['name'],
                'description' => $r['description'] ?? null,
                'permissions' => $r['permissions'] ?? null,
                'users_count' => $r['users_count'] ?? 0,
            ]);
        }
    }

    private function seedAdminUser(): void
    {
        $adminRole = Role::where('name', 'Super Admin')->first()
            ?? Role::first();

        User::updateOrCreate(
            ['email' => 'admin@bluezone.com'],
            [
                'name' => 'Tariq M.',
                'password' => bcrypt('password'),
                'role_id' => $adminRole?->id,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
    }
}
