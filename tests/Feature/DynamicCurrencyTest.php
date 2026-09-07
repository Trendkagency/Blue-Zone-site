<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class DynamicCurrencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::clearCache();
    }

    protected function tearDown(): void
    {
        Setting::clearCache();
        parent::tearDown();
    }

    public function test_currency_service_formats_supported_currencies_accurately(): void
    {
        // 1. SAR formatting
        $this->assertEquals('150.00 ر.س', CurrencyService::format(150, 'SAR', 'ar'));
        $this->assertEquals('150.00 SAR', CurrencyService::format(150, 'SAR', 'en'));

        // 2. USD formatting
        $this->assertEquals('$150.00', CurrencyService::format(150, 'USD', 'en'));
        $this->assertEquals('$150.00', CurrencyService::format(150, 'USD', 'ar'));

        // 3. EUR formatting
        $this->assertEquals('€99.50', CurrencyService::format(99.5, 'EUR', 'en'));

        // 4. AED formatting
        $this->assertEquals('250.00 د.إ', CurrencyService::format(250, 'AED', 'ar'));
        $this->assertEquals('250.00 AED', CurrencyService::format(250, 'AED', 'en'));

        // 5. GBP formatting
        $this->assertEquals('£75.00', CurrencyService::format(75, 'GBP', 'en'));

        // 6. KWD formatting
        $this->assertEquals('45.00 د.ك', CurrencyService::format(45, 'KWD', 'ar'));
    }

    public function test_currency_blade_directives_compile_and_render(): void
    {
        Setting::set('currency', 'SAR');
        app()->setLocale('ar');

        $rendered = Blade::render('@currency(180)');
        $this->assertStringContainsString('180.00', $rendered);
        $this->assertStringContainsString('ر.س', $rendered);

        $symbol = Blade::render('@currencySymbol');
        $this->assertStringContainsString('ر.س', $symbol);

        $code = Blade::render('@currencyCode');
        $this->assertEquals('SAR', trim($code));
    }

    public function test_currency_helper_function_works(): void
    {
        Setting::set('currency', 'USD');
        $this->assertEquals('$299.00', format_currency(299));
        $this->assertEquals('USD', currency_code());
        $this->assertEquals('$', currency_symbol());
    }

    public function test_custom_symbol_override_is_respected(): void
    {
        Setting::set('currency', 'SAR');
        Setting::set('currency_symbol', '﷼');

        $this->assertEquals('﷼', CurrencyService::symbol('SAR'));
        $this->assertStringContainsString('﷼', CurrencyService::format(100, 'SAR'));

        // Clear override
        Setting::set('currency_symbol', '');
    }

    public function test_currency_position_and_decimals_configuration(): void
    {
        Setting::set('currency', 'USD');
        Setting::set('currency_position', 'after');
        Setting::set('currency_decimals', 0);

        $this->assertEquals('100 $', CurrencyService::format(100));

        Setting::set('currency_position', 'before');
        Setting::set('currency_decimals', 3);
        $this->assertEquals('$100.000', CurrencyService::format(100));
    }

    public function test_admin_settings_controller_saves_currency_parameters(): void
    {
        $role = Role::create([
            'name' => 'Super Admin',
            'description' => 'Super Admin',
            'permissions' => ['*'],
        ]);

        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin_curr_test@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'currency' => 'AED',
            'currency_position' => 'after',
            'currency_decimals' => 2,
            'currency_symbol' => 'د.إ',
            'tax_percentage' => 15,
            'tax_number' => '31004829100003',
        ]);

        $response->assertRedirect();
        Setting::clearCache();

        $this->assertEquals('AED', Setting::get('currency'));
        $this->assertEquals('after', Setting::get('currency_position'));
        $this->assertEquals(2, (int) Setting::get('currency_decimals'));
        $this->assertEquals('د.إ', Setting::get('currency_symbol'));
    }

    public function test_storefront_renders_prices_in_dynamically_selected_currency(): void
    {
        // 1. Set to SAR
        Setting::set('currency', 'SAR');
        Setting::set('currency_symbol', '');
        Setting::set('currency_position', 'after');
        Setting::set('currency_decimals', 2);
        Setting::clearCache();

        $res = $this->get(route('customer.home'));
        $res->assertOk();
        $body = $res->getContent();
        $hasSar = str_contains($body, 'SAR') || str_contains($body, 'ر.س');
        $this->assertTrue($hasSar, 'Expected homepage to render prices with SAR / ر.س currency symbol');

        // 2. Set to USD
        Setting::set('currency', 'USD');
        Setting::set('currency_position', 'before');
        Setting::clearCache();

        $resUsd = $this->get(route('customer.home'));
        $resUsd->assertOk();
        $this->assertStringContainsString('$', $resUsd->getContent());
    }

    public function test_javascript_currency_config_injected_in_layout(): void
    {
        Setting::set('currency', 'SAR');
        Setting::clearCache();

        $response = $this->get(route('customer.home'));
        $response->assertOk();
        $response->assertSee('window.BLUEZONE_CURRENCY', false);
        $response->assertSee('code: "SAR"', false);
    }
}
