<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\CartService;
use App\Services\InventoryService;
use App\Services\Payment\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartAndCheckoutCycleTest extends TestCase
{
    use RefreshDatabase;

    protected Product $product1;
    protected Product $product2;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'name_en' => 'Cognitive',
            'name_ar' => 'الإدراك والتركيز',
            'slug' => 'cognitive',
            'is_active' => true,
        ]);

        $this->product1 = Product::create([
            'category_id' => $category->id,
            'name_en' => 'BLUE MIND',
            'name_ar' => 'بلو مايند',
            'slug' => 'blue-mind',
            'sku' => 'BZ-MIND-01',
            'price' => 68.00,
            'cost_price' => 25.00,
            'stock_online' => 50,
            'stock_offline' => 20,
            'low_stock_threshold' => 10,
            'status' => 'Active',
            'is_featured' => true,
        ]);

        $this->product2 = Product::create([
            'category_id' => $category->id,
            'name_en' => 'BLUE ENERGY',
            'name_ar' => 'بلو إنرجي',
            'slug' => 'blue-energy',
            'sku' => 'BZ-ENRG-01',
            'price' => 58.00,
            'cost_price' => 20.00,
            'stock_online' => 30,
            'stock_offline' => 15,
            'low_stock_threshold' => 5,
            'status' => 'Active',
            'is_featured' => false,
        ]);

        $role = \App\Models\Role::create([
            'name' => 'Super Admin',
            'slug' => 'super_admin',
            'description' => 'Full Administrative Access',
            'permissions' => ['*'],
        ]);

        $this->adminUser = User::create([
            'name' => 'Admin Operator',
            'email' => 'admin@bluezone.com',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        // Clear session cart
        CartService::clear();
    }

    public function test_cart_service_can_add_update_and_remove_items(): void
    {
        // 1. Add product1
        $summary = CartService::add($this->product1, 2);
        $this->assertEquals(2, $summary['count']);
        $this->assertEquals(136.00, $summary['subtotal']);

        // 2. Add product2
        $summary = CartService::add($this->product2, 1);
        $this->assertEquals(3, $summary['count']);
        $this->assertEquals(194.00, $summary['subtotal']);

        // 3. Update quantity of product1
        $summary = CartService::updateQuantity($this->product1->id, 1);
        $this->assertEquals(2, $summary['count']);
        $this->assertEquals(126.00, $summary['subtotal']);

        // 4. Remove product2
        $summary = CartService::remove($this->product2->id);
        $this->assertEquals(1, $summary['count']);
        $this->assertEquals(68.00, $summary['subtotal']);

        // 5. Clear cart
        CartService::clear();
        $this->assertEquals(0, CartService::getCount());
        $this->assertEquals(0.0, CartService::getSubtotal());
    }

    public function test_cart_coupon_application_and_tax_and_shipping_math(): void
    {
        // Free shipping threshold is $75. Tax is 15%.
        Setting::set('free_shipping_threshold', 75.00, 'shipping');
        Setting::set('flat_shipping_rate', 10.00, 'shipping');
        Setting::set('tax_percentage', 15.00, 'tax');
        Setting::set('enable_tax', true, 'tax');

        // Add 1 product at $68.00 (< $75 threshold -> shipping = $10.00)
        CartService::add($this->product1, 1);

        $summary = CartService::getSummary();
        $this->assertEquals(68.00, $summary['subtotal']);
        $this->assertEquals(10.00, $summary['shipping']);
        // Tax 15% of 68 = 10.20
        $this->assertEquals(10.20, $summary['tax']);
        // Total = 68 + 10 + 10.20 = 88.20
        $this->assertEquals(88.20, $summary['total']);

        // Apply 15% coupon WELCOME15
        CartService::applyCoupon('WELCOME15');
        $summary = CartService::getSummary();
        // Discount 15% of 68 = 10.20
        $this->assertEquals(10.20, $summary['discount']);
        // Taxable = 68 - 10.20 = 57.80. Tax 15% of 57.80 = 8.67
        $this->assertEquals(8.67, $summary['tax']);
        // Total = 57.80 + 10 (shipping) + 8.67 = 76.47
        $this->assertEquals(76.47, $summary['total']);

        // Add second product to surpass $75 threshold
        CartService::add($this->product2, 1); // 68 + 58 = 126
        $summary = CartService::getSummary();
        $this->assertTrue($summary['free_shipping_unlocked']);
        $this->assertEquals(0.0, $summary['shipping']);
    }

    public function test_cart_endpoints_work_via_http(): void
    {
        // Test GET /cart
        $res = $this->get(route('customer.cart'));
        $res->assertStatus(200);

        // Test POST /cart/add
        $res = $this->postJson(route('customer.cart.add'), [
            'product_id' => $this->product1->id,
            'quantity' => 2,
        ]);
        $res->assertStatus(200);
        $res->assertJsonPath('success', true);
        $res->assertJsonPath('cart.count', 2);

        // Test GET /cart/items
        $res = $this->getJson(route('customer.cart.items'));
        $res->assertStatus(200);
        $res->assertJsonPath('count', 2);

        // Test POST /cart/coupon
        $res = $this->postJson(route('customer.cart.coupon'), [
            'code' => 'WELCOME15',
        ]);
        $res->assertStatus(200);
        $res->assertJsonPath('cart.coupon.code', 'WELCOME15');

        // Test DELETE /cart/coupon
        $res = $this->deleteJson(route('customer.cart.coupon.remove'));
        $res->assertStatus(200);
        $res->assertJsonPath('cart.coupon', null);
    }

    public function test_checkout_page_redirects_if_cart_is_empty(): void
    {
        CartService::clear();
        $res = $this->get(route('customer.checkout'));
        $res->assertRedirect(route('customer.cart'));
    }

    public function test_checkout_places_order_with_cod_and_deducts_online_inventory(): void
    {
        CartService::add($this->product1, 2);
        $initialStock = $this->product1->fresh()->stock_online;

        $checkoutData = [
            'full_name' => 'Fahad Al-Otaibi',
            'email' => 'fahad@example.com',
            'phone' => '+966 55 987 6543',
            'address' => 'Olaya Towers, 14th Floor',
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
            'postal_code' => '12214',
            'notes' => 'Please deliver between 10am and 2pm.',
            'payment_method' => 'cod',
        ];

        $res = $this->post(route('customer.checkout.store'), $checkoutData);

        // Verify Order Created
        $order = Order::where('customer_email', 'fahad@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals('cod', $order->payment_method);
        $this->assertEquals('Pending', $order->payment_status);
        $this->assertEquals('Confirmed', $order->status);
        $this->assertStringStartsWith('COD-', $order->payment_transaction_id);

        // Verify OrderItem created
        $this->assertCount(1, $order->items);
        $this->assertEquals($this->product1->id, $order->items->first()->product_id);
        $this->assertEquals(2, $order->items->first()->quantity);

        // Verify Online Inventory Deducted
        $this->assertEquals($initialStock - 2, $this->product1->fresh()->stock_online);

        // Verify Cart is cleared
        $this->assertEquals(0, CartService::getCount());

        // Verify Redirected to Confirmation
        $res->assertRedirect(route('customer.checkout.confirmation', $order->order_number));

        // Verify Confirmation page loads
        $confirmRes = $this->get(route('customer.checkout.confirmation', $order->order_number));
        $confirmRes->assertStatus(200);
        $confirmRes->assertSee($order->order_number);
    }

    public function test_checkout_places_order_with_stripe_card(): void
    {
        CartService::add($this->product2, 1);

        $checkoutData = [
            'full_name' => 'Sarah Johnson',
            'email' => 'sarah.j@example.com',
            'phone' => '+1 415 555 2671',
            'address' => '450 Sutter St, Suite 1200',
            'city' => 'San Francisco',
            'country' => 'United States',
            'postal_code' => '94108',
            'payment_method' => 'stripe',
        ];

        $res = $this->post(route('customer.checkout.store'), $checkoutData);

        $order = Order::where('customer_email', 'sarah.j@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals('card', $order->payment_method);
        $this->assertEquals('stripe', $order->payment_gateway);
        $this->assertEquals('Paid', $order->payment_status);
        $this->assertEquals('Processing', $order->status);
        $this->assertNotNull($order->payment_transaction_id);

        $res->assertRedirect(route('customer.checkout.confirmation', $order->order_number));
    }

    public function test_webhook_updates_order_status_to_paid(): void
    {
        $order = Order::create([
            'order_number' => 'BZ-TEST-WH-01',
            'invoice_number' => 'INV-TEST-01',
            'channel' => 'online',
            'customer_name' => 'Webhook Test User',
            'customer_email' => 'webhook@bluezone.com',
            'date' => now()->toDateString(),
            'status' => 'Pending',
            'payment_method' => 'stripe',
            'payment_gateway' => 'stripe',
            'payment_status' => 'Pending',
            'subtotal' => 68.00,
            'total' => 68.00,
        ]);

        $payload = [
            'id' => 'evt_test_123',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_wh_xyz',
                    'client_reference_id' => $order->order_number,
                    'metadata' => [
                        'order_number' => $order->order_number,
                    ],
                ],
            ],
        ];

        $res = $this->postJson('/webhooks/payment/stripe?simulate_test=1', $payload);
        $res->assertStatus(200);
        $res->assertJsonPath('received', true);
        $res->assertJsonPath('status', 'Paid');

        $this->assertEquals('Paid', $order->fresh()->payment_status);
        $this->assertEquals('Processing', $order->fresh()->status);
        $this->assertEquals('cs_test_wh_xyz', $order->fresh()->payment_transaction_id);
    }

    public function test_admin_can_update_payment_settings(): void
    {
        $this->actingAs($this->adminUser, 'web');

        $updatePayload = [
            'tax_percentage' => 15,
            'tax_number' => '31004829100003',
            'enable_tax' => true,
            'payment_stripe_enabled' => true,
            'payment_stripe_mode' => 'live',
            'payment_stripe_public_key' => 'pk_live_custom_key_for_test',
            'payment_stripe_secret_key' => 'sk_live_custom_secret_for_test',
            'payment_stripe_webhook_secret' => 'whsec_custom_webhook_for_test',
            'payment_cod_enabled' => true,
            'payment_cod_extra_fee' => 5.00,
            'payment_default_gateway' => 'stripe',
        ];

        $res = $this->post(route('admin.settings.update'), $updatePayload);
        $res->assertRedirect(route('admin.settings.index'));

        $this->assertEquals('live', Setting::get('payment_stripe_mode'));
        $this->assertEquals('pk_live_custom_key_for_test', Setting::get('payment_stripe_public_key'));
        $this->assertEquals('sk_live_custom_secret_for_test', Setting::get('payment_stripe_secret_key'));
        $this->assertEquals('whsec_custom_webhook_for_test', Setting::get('payment_stripe_webhook_secret'));
        $this->assertEquals(5.00, (float) Setting::get('payment_cod_extra_fee'));
    }
}
