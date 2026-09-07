<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Setting;
use App\Models\User;
use App\Services\CaptchaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CaptchaAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure settings table is available and fresh
        Setting::truncate();
        Setting::set('enable_captcha_login', true);
        Setting::set('enable_captcha_register', true);
        Setting::set('enable_captcha_forgot_password', true);
        Setting::set('enable_captcha_admin_login', true);
    }

    public function test_captcha_renders_on_customer_login_page(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('bz-captcha-wrapper');
        $response->assertSee('bz-captcha-reload');
        $response->assertSee('inputmode="numeric"', false);
    }

    public function test_captcha_renders_on_customer_register_page(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('bz-captcha-wrapper');
        $response->assertSee('name="captcha"', false);
    }

    public function test_captcha_renders_on_customer_forgot_password_page(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('bz-captcha-wrapper');
        $response->assertSee('name="captcha"', false);
    }

    public function test_captcha_renders_on_admin_login_page(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('bz-captcha-wrapper');
        $response->assertSee('name="captcha"', false);
    }

    public function test_captcha_refresh_endpoint_returns_valid_svg(): void
    {
        $response = $this->getJson('/captcha/refresh');

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'svg']);
        $response->assertJson(['success' => true]);
        $this->assertStringContainsString('<svg', $response->json('svg'));
    }

    public function test_customer_login_fails_with_invalid_captcha(): void
    {
        $customer = Customer::create([
            'name' => 'Dr. Layla Test',
            'email' => 'layla@example.com',
            'password' => Hash::make('secret1234'),
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => 'layla@example.com',
            'password' => 'secret1234',
            'captcha' => 'wrong_answer_999',
        ]);

        $response->assertSessionHasErrors('captcha');
        $this->assertGuest('customer');
    }

    public function test_customer_login_succeeds_with_valid_captcha(): void
    {
        $customer = Customer::create([
            'name' => 'Dr. Layla Test',
            'email' => 'layla@example.com',
            'password' => Hash::make('secret1234'),
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => 'layla@example.com',
            'password' => 'secret1234',
            'captcha' => 'bypass',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($customer, 'customer');
    }

    public function test_customer_register_fails_with_invalid_captcha(): void
    {
        $response = $this->post('/register', [
            'name' => 'Ali Mansoor',
            'email' => 'ali.mansoor@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'captcha' => 'invalid_code',
        ]);

        $response->assertSessionHasErrors('captcha');
        $this->assertDatabaseMissing('customers', ['email' => 'ali.mansoor@example.com']);
    }

    public function test_admin_login_fails_with_invalid_captcha(): void
    {
        $admin = User::create([
            'name' => 'Security Admin',
            'email' => 'secadmin@bluezone.com',
            'password' => Hash::make('admin12345'),
            'role' => 'admin',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'secadmin@bluezone.com',
            'password' => 'admin12345',
            'captcha' => 'wrong_math',
        ]);

        $response->assertSessionHasErrors('captcha');
        $this->assertGuest('web');
    }

    public function test_captcha_can_be_disabled_via_settings(): void
    {
        Setting::set('enable_captcha_login', false);

        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertDontSee('bz-captcha-wrapper');
    }
}
