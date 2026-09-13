<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Setting;
use Database\Seeders\WorldCountriesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerRegistrationGeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('enable_captcha_register', false);
        $this->seed(WorldCountriesSeeder::class);
    }

    public function test_register_page_loads_dynamic_countries(): void
    {
        $response = $this->get(route('customer.auth.register'));
        $response->assertStatus(200);
        $response->assertSee('Saudi Arabia');
        $response->assertSee('+966');
        $response->assertSee('phone_code_badge');
    }

    public function test_public_geo_cities_api_returns_country_and_cities(): void
    {
        $sa = Country::where('iso2', 'SA')->first();
        City::create([
            'country_id' => $sa->id,
            'name_en' => 'Riyadh',
            'name_ar' => 'الرياض',
            'shipping_cost' => 25.00,
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/geo/countries/{$sa->id}/cities");
        $response->assertStatus(200);
        $response->assertJsonPath('country.phone_code', '+966');
        $response->assertJsonPath('cities.0.name_en', 'Riyadh');
    }

    public function test_customer_registration_with_dynamic_country_and_city(): void
    {
        $uae = Country::where('iso2', 'AE')->first();
        $dubai = City::create([
            'country_id' => $uae->id,
            'name_en' => 'Dubai',
            'name_ar' => 'دبي',
            'shipping_cost' => 35.00,
            'is_active' => true,
        ]);

        $payload = [
            'name' => 'Sara Al-Maktoum',
            'email' => 'sara@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'country_id' => $uae->id,
            'city_id' => $dubai->id,
            'phone' => '501234567',
            'phone_code' => '+971',
            'address' => 'Downtown Blvd, Dubai Mall Area',
        ];

        $response = $this->post(route('customer.auth.register.submit'), $payload);
        $response->assertRedirect(route('customer.account.dashboard'));

        $this->assertAuthenticatedAs(Customer::where('email', 'sara@example.com')->first(), 'customer');

        $customer = Customer::where('email', 'sara@example.com')->first();
        $this->assertNotNull($customer);
        $this->assertEquals('United Arab Emirates', $customer->country);
        $this->assertEquals('Dubai', $customer->city);
        $this->assertStringContainsString('+971', $customer->phone);
    }

    public function test_customer_registration_with_custom_city(): void
    {
        $egypt = Country::where('iso2', 'EG')->first();

        $payload = [
            'name' => 'Ahmed Hassan',
            'email' => 'ahmed@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'country_id' => $egypt->id,
            'city' => 'El Gouna',
            'phone' => '1012345678',
            'phone_code' => '+20',
            'address' => 'Red Sea District',
        ];

        $response = $this->post(route('customer.auth.register.submit'), $payload);
        $response->assertRedirect(route('customer.account.dashboard'));

        $customer = Customer::where('email', 'ahmed@example.com')->first();
        $this->assertNotNull($customer);
        $this->assertEquals('Egypt', $customer->country);
        $this->assertEquals('El Gouna', $customer->city);
        $this->assertStringContainsString('+20', $customer->phone);
    }
}
