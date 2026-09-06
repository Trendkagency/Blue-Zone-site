<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAccountTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;
    protected Product $product;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'slug' => 'cognitive-health',
            'name_en' => 'Cognitive Health',
            'name_ar' => 'الصحة الإدراكية',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'slug' => 'blue-mind',
            'sku' => 'BZ-MND-001',
            'name_en' => 'BLUE MIND Precision Nootropic',
            'name_ar' => 'بلو مايند',
            'category_id' => $this->category->id,
            'price' => 68.00,
            'cost_price' => 22.00,
            'stock_online' => 100,
            'stock_offline' => 50,
            'status' => 'active',
        ]);

        $this->customer = Customer::create([
            'name' => 'Dr. Zaid Al-Harbi',
            'email' => 'zaid.harbi@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+966 50 123 4567',
            'address' => '742 Longevity Way, King Fahd District',
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
            'postal_code' => '12271',
            'status' => 'active',
            'loyalty_points' => 100,
            'registered_at' => now(),
        ]);
    }

    public function test_customer_registration_with_full_account_fields(): void
    {
        $response = $this->post(route('customer.auth.register.submit'), [
            'name' => 'Dr. Layla Mansoor',
            'email' => 'layla.mansoor@example.com',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
            'phone' => '+966 55 999 1122',
            'address' => 'Al-Olaya Tower, Floor 14',
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
        ]);

        $response->assertRedirect(route('customer.account.dashboard'));

        $this->assertDatabaseHas('customers', [
            'email' => 'layla.mansoor@example.com',
            'name' => 'Dr. Layla Mansoor',
            'city' => 'Riyadh',
            'loyalty_points' => 100,
        ]);

        $newCustomer = Customer::where('email', 'layla.mansoor@example.com')->first();
        $this->assertNotEmpty($newCustomer->saved_addresses);
    }

    public function test_customer_login_and_dashboard_access(): void
    {
        $loginRes = $this->post(route('customer.auth.login.submit'), [
            'email' => 'zaid.harbi@example.com',
            'password' => 'password123',
        ]);

        $loginRes->assertRedirect(route('customer.account.dashboard'));
        $this->assertAuthenticatedAs($this->customer, 'customer');

        $dashRes = $this->actingAs($this->customer, 'customer')->get(route('customer.account.dashboard'));
        $dashRes->assertStatus(200);
        $dashRes->assertSee('Dr. Zaid Al-Harbi');
    }

    public function test_customer_profile_update(): void
    {
        $response = $this->actingAs($this->customer, 'customer')->put(route('customer.account.profile.update'), [
            'name' => 'Dr. Zaid M. Al-Harbi',
            'phone' => '+966 50 999 8877',
            'email' => 'zaid.harbi@example.com',
            'address' => 'Villa 44, Diplomatic Quarter',
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
            'postal_code' => '11564',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->customer->refresh();
        $this->assertEquals('Dr. Zaid M. Al-Harbi', $this->customer->name);
        $this->assertEquals('+966 50 999 8877', $this->customer->phone);
        $this->assertEquals('Villa 44, Diplomatic Quarter', $this->customer->address);
    }

    public function test_customer_password_update_with_current_password_verification(): void
    {
        // Wrong current password
        $failRes = $this->actingAs($this->customer, 'customer')->put(route('customer.account.password.update'), [
            'current_password' => 'wrongpassword',
            'password' => 'brandnewpassword123',
            'password_confirmation' => 'brandnewpassword123',
        ]);
        $failRes->assertSessionHasErrors('current_password');

        // Correct current password
        $passRes = $this->actingAs($this->customer, 'customer')->put(route('customer.account.password.update'), [
            'current_password' => 'password123',
            'password' => 'brandnewpassword123',
            'password_confirmation' => 'brandnewpassword123',
        ]);
        $passRes->assertSessionHas('success');

        $this->customer->refresh();
        $this->assertTrue(Hash::check('brandnewpassword123', $this->customer->password));
    }

    public function test_saved_addresses_crud(): void
    {
        // Add new address
        $addRes = $this->actingAs($this->customer, 'customer')->post(route('customer.account.addresses.store'), [
            'title' => 'King Faisal Specialist Hospital Office',
            'recipient' => 'Dr. Zaid Al-Harbi',
            'phone' => '+966 50 123 4567',
            'street' => 'Zahrawi St, Medical District',
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
            'postal_code' => '11211',
            'is_default' => '1',
        ]);
        $addRes->assertSessionHas('success');

        $this->customer->refresh();
        $addresses = $this->customer->getAddressesList();
        $this->assertCount(2, $addresses);

        $newAddr = end($addresses);
        $this->assertEquals('King Faisal Specialist Hospital Office', $newAddr['title']);
        $this->assertTrue($newAddr['is_default']);

        // Update address
        $updateRes = $this->actingAs($this->customer, 'customer')->put(route('customer.account.addresses.update', $newAddr['id']), [
            'title' => 'Main Medical Office Suite 4B',
            'recipient' => 'Dr. Zaid Al-Harbi',
            'phone' => '+966 50 123 4567',
            'street' => 'Zahrawi St, Medical District',
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
            'postal_code' => '11211',
        ]);
        $updateRes->assertSessionHas('success');

        $this->customer->refresh();
        $updatedAddresses = $this->customer->getAddressesList();
        $updatedAddr = end($updatedAddresses);
        $this->assertEquals('Main Medical Office Suite 4B', $updatedAddr['title']);

        // Delete address
        $delRes = $this->actingAs($this->customer, 'customer')->delete(route('customer.account.addresses.destroy', $newAddr['id']));
        $delRes->assertSessionHas('success');

        $this->customer->refresh();
        $this->assertCount(1, $this->customer->getAddressesList());
    }

    public function test_orders_listing_and_show_with_timeline(): void
    {
        $order = Order::create([
            'order_number' => 'BZ-ORD-2026-CUST1',
            'invoice_number' => 'INV-2026-CUST1',
            'channel' => 'online',
            'customer_name' => $this->customer->name,
            'customer_email' => $this->customer->email,
            'customer_id' => $this->customer->id,
            'date' => now()->toDateString(),
            'status' => 'processing',
            'payment_method' => 'Mada',
            'payment_status' => 'paid',
            'subtotal' => 68.00,
            'tax' => 10.20,
            'total' => 78.20,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name_en' => $this->product->name_en,
            'variant_en' => 'Standard Pack',
            'unit_price' => 68.00,
            'quantity' => 1,
            'total' => 68.00,
        ]);

        $ordersListRes = $this->actingAs($this->customer, 'customer')->get(route('customer.account.orders'));
        $ordersListRes->assertStatus(200);
        $ordersListRes->assertSee('BZ-ORD-2026-CUST1');

        $orderShowRes = $this->actingAs($this->customer, 'customer')->get(route('customer.account.orders.show', $order->order_number));
        $orderShowRes->assertStatus(200);
        $orderShowRes->assertSee('BZ-ORD-2026-CUST1');
        $orderShowRes->assertSee('BLUE MIND Precision Nootropic');
    }

    public function test_1_click_reorder_copies_items_into_cart(): void
    {
        $order = Order::create([
            'order_number' => 'BZ-ORD-REORDER-1',
            'invoice_number' => 'INV-REORDER-1',
            'channel' => 'online',
            'customer_name' => $this->customer->name,
            'customer_email' => $this->customer->email,
            'customer_id' => $this->customer->id,
            'date' => now()->toDateString(),
            'status' => 'delivered',
            'payment_method' => 'Mada',
            'payment_status' => 'paid',
            'subtotal' => 136.00,
            'tax' => 20.40,
            'total' => 156.40,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name_en' => $this->product->name_en,
            'variant_en' => 'Standard Pack',
            'unit_price' => 68.00,
            'quantity' => 2,
            'total' => 136.00,
        ]);

        $reorderRes = $this->actingAs($this->customer, 'customer')->post(route('customer.account.orders.reorder', $order->order_number));
        $reorderRes->assertRedirect(route('customer.cart'));

        $this->assertEquals(2, session('cart')[$this->product->id]['quantity']);
    }

    public function test_wishlist_toggle_and_view(): void
    {
        // Toggle add to wishlist
        $addWishRes = $this->actingAs($this->customer, 'customer')->post(route('customer.account.wishlist.toggle'), [
            'product_id' => $this->product->id,
        ]);
        $addWishRes->assertRedirect();

        $this->customer->refresh();
        $this->assertContains($this->product->id, $this->customer->wishlist);

        // View wishlist page
        $viewWishRes = $this->actingAs($this->customer, 'customer')->get(route('customer.account.wishlist'));
        $viewWishRes->assertStatus(200);
        $viewWishRes->assertSee('BLUE MIND');

        // Toggle remove
        $removeWishRes = $this->actingAs($this->customer, 'customer')->post(route('customer.account.wishlist.toggle'), [
            'product_id' => $this->product->id,
        ]);
        $removeWishRes->assertRedirect();

        $this->customer->refresh();
        $this->assertNotContains($this->product->id, $this->customer->wishlist);
    }

    public function test_settings_update_notification_preferences(): void
    {
        $response = $this->actingAs($this->customer, 'customer')->put(route('customer.account.settings.update'), [
            'email_orders' => '1',
            'email_science' => '0',
            'sms_orders' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->customer->refresh();
        $this->assertTrue($this->customer->notification_preferences['email_orders']);
        $this->assertFalse($this->customer->notification_preferences['email_science']);
        $this->assertTrue($this->customer->notification_preferences['sms_orders']);
    }
}
