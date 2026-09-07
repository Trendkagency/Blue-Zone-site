<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUsersAndClientsControlTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $restrictedUser;
    protected Role $adminRole;
    protected Role $restrictedRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create([
            'name' => 'Super Admin',
            'description' => 'Super Administrator with full access',
            'permissions' => ['*'],
        ]);

        $this->restrictedRole = Role::create([
            'name' => 'Limited Staff',
            'description' => 'Staff without users or customers permission',
            'permissions' => [
                'products' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
            ],
        ]);

        $this->superAdmin = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active',
        ]);

        $this->restrictedUser = User::factory()->create([
            'role_id' => $this->restrictedRole->id,
            'status' => 'active',
        ]);
    }

    public function test_super_admin_can_view_users_index_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee($this->superAdmin->name);
        $response->assertSee($this->restrictedUser->name);
    }

    public function test_super_admin_can_view_user_show_profile_and_permission_matrix(): void
    {
        $targetUser = User::factory()->create([
            'name' => 'Dr. Zaid Longevity',
            'email' => 'zaid@bluezone.com',
            'phone' => '+966 55 123 4567',
            'bio' => 'Lead cellular optimization lead',
            'role_id' => $this->adminRole->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.users.show', $targetUser->id));

        $response->assertStatus(200);
        $response->assertSee('Dr. Zaid Longevity');
        $response->assertSee('zaid@bluezone.com');
        $response->assertSee('+966 55 123 4567');
        $response->assertSee('Lead cellular optimization lead');
        $response->assertSee('Super Admin');
    }

    public function test_super_admin_can_toggle_user_status(): void
    {
        $targetUser = User::factory()->create([
            'name' => 'Staff Subject',
            'email' => 'subject@bluezone.com',
            'status' => 'active',
            'role_id' => $this->restrictedRole->id,
        ]);

        // Toggle from active -> suspended
        $response = $this->actingAs($this->superAdmin)->from(route('admin.users.index'))->post(route('admin.users.toggle-status', $targetUser->id));
        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertEquals('suspended', $targetUser->fresh()->status);

        // Toggle from suspended -> active
        $response = $this->actingAs($this->superAdmin)->from(route('admin.users.index'))->post(route('admin.users.toggle-status', $targetUser->id));
        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertEquals('active', $targetUser->fresh()->status);
    }

    public function test_super_admin_cannot_suspend_themselves(): void
    {
        $response = $this->actingAs($this->superAdmin)->from(route('admin.users.index'))->post(route('admin.users.toggle-status', $this->superAdmin->id));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertEquals('active', $this->superAdmin->fresh()->status);
    }

    public function test_super_admin_can_view_customers_index(): void
    {
        $customer = Customer::create([
            'name' => 'VIP Client Al-Otaibi',
            'email' => 'otaibi@client.com',
            'phone' => '+966 50 999 8888',
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
            'tier' => 'VIP',
            'status' => 'active',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.customers.index'));

        $response->assertStatus(200);
        $response->assertSee('VIP Client Al-Otaibi');
        $response->assertSee('otaibi@client.com');
    }

    public function test_super_admin_can_view_customer_show_dossier_with_orders_and_stats(): void
    {
        $customer = Customer::create([
            'name' => 'Princess Noura',
            'email' => 'noura@client.com',
            'phone' => '+966 50 111 2222',
            'city' => 'Jeddah',
            'address' => 'Corniche Road Luxury Villa 12',
            'country' => 'Saudi Arabia',
            'tier' => 'VIP',
            'status' => 'active',
            'password' => bcrypt('password'),
        ]);

        $order = Order::create([
            'order_number' => 'BZ-99201',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'status' => 'delivered',
            'payment_status' => 'paid',
            'subtotal' => 500.00,
            'total' => 575.00,
            'date' => now(),
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.customers.show', $customer->id));

        $response->assertStatus(200);
        $response->assertSee('Princess Noura');
        $response->assertSee('BZ-99201');
        $response->assertSee('Corniche Road Luxury Villa 12');
        $response->assertSee(format_currency(575.00));
        $response->assertDontSee('app.fields.');
        $response->assertDontSee('admin.customers.');
        $response->assertSee('customer-dossier-grid');

        // Test in Arabic locale
        app()->setLocale('ar');
        session(['locale' => 'ar']);
        $responseAr = $this->actingAs($this->superAdmin)->withSession(['locale' => 'ar'])->get(route('admin.customers.show', $customer->id));
        $responseAr->assertStatus(200);
        $responseAr->assertDontSee('app.fields.');
        $responseAr->assertDontSee('admin.customers.');
        $responseAr->assertSee('بيانات التواصل');
        $responseAr->assertSee('العناوين المحفوظة');
        app()->setLocale('en');
    }

    public function test_super_admin_can_toggle_customer_status(): void
    {
        $customer = Customer::create([
            'name' => 'Client To Toggle',
            'email' => 'toggle@client.com',
            'phone' => '+966 50 333 4444',
            'status' => 'active',
            'password' => bcrypt('password'),
        ]);

        // Toggle from active -> inactive
        $response = $this->actingAs($this->superAdmin)->from(route('admin.customers.index'))->post(route('admin.customers.toggle-status', $customer->id));
        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertEquals('inactive', $customer->fresh()->status);

        // Toggle from inactive -> active
        $response = $this->actingAs($this->superAdmin)->from(route('admin.customers.index'))->post(route('admin.customers.toggle-status', $customer->id));
        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertEquals('active', $customer->fresh()->status);
    }

    public function test_restricted_user_cannot_access_users_without_permission(): void
    {
        $response = $this->actingAs($this->restrictedUser)->get(route('admin.users.index'));
        $response->assertStatus(403);

        $responseShow = $this->actingAs($this->restrictedUser)->get(route('admin.users.show', $this->superAdmin->id));
        $responseShow->assertStatus(403);
    }

    public function test_restricted_user_cannot_access_customers_without_permission(): void
    {
        $customer = Customer::create([
            'name' => 'Protected Client',
            'email' => 'protected@client.com',
            'status' => 'active',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($this->restrictedUser)->get(route('admin.customers.index'));
        $response->assertStatus(403);

        $responseShow = $this->actingAs($this->restrictedUser)->get(route('admin.customers.show', $customer->id));
        $responseShow->assertStatus(403);
    }
}
