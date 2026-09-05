<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Services\TypographyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TypographySettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_typography_service_provides_curated_collection_of_fonts(): void
    {
        $fonts = TypographyService::getAvailableFonts();

        $this->assertGreaterThanOrEqual(15, count($fonts));
        $this->assertArrayHasKey('Mont Blanc', $fonts);
        $this->assertArrayHasKey('Cairo', $fonts);
        $this->assertArrayHasKey('Tajawal', $fonts);
        $this->assertArrayHasKey('Almarai', $fonts);
        $this->assertArrayHasKey('Readex Pro', $fonts);
        $this->assertArrayHasKey('Alexandria', $fonts);
        $this->assertArrayHasKey('IBM Plex Sans Arabic', $fonts);
        $this->assertArrayHasKey('Inter', $fonts);
        $this->assertArrayHasKey('Plus Jakarta Sans', $fonts);
    }

    public function test_typography_service_builds_valid_google_and_bunny_fonts_url(): void
    {
        $googleUrl = TypographyService::buildGoogleFontsUrl(['Cairo', 'Tajawal']);
        $this->assertStringContainsString('https://fonts.googleapis.com/css2?', $googleUrl);
        $this->assertStringContainsString('family=Cairo:wght@', $googleUrl);

        $bunnyUrl = TypographyService::buildBunnyFontsUrl(['Cairo', 'Tajawal']);
        $this->assertStringContainsString('https://fonts.bunny.net/css?', $bunnyUrl);
        $this->assertStringContainsString('cairo:', $bunnyUrl);
        $this->assertStringContainsString('tajawal:', $bunnyUrl);
    }

    public function test_storefront_loads_local_fontawesome_asset(): void
    {
        $response = $this->get(route('customer.home'));
        $response->assertStatus(200);
        $response->assertSee('vendor/fontawesome/css/all.min.css');
        $this->assertFileExists(public_path('vendor/fontawesome/css/all.min.css'));
    }

    public function test_active_typography_config_reflects_settings_with_fallback(): void
    {
        $defaultConfig = TypographyService::getActiveConfig();
        $this->assertEquals('Mont Blanc', $defaultConfig['font_family']);

        Setting::set('font_family', 'Almarai');
        Setting::set('font_heading_family', 'Alexandria');
        Setting::set('font_size_base', '17px');
        Setting::set('font_weight_headings', '800');

        $updatedConfig = TypographyService::getActiveConfig();
        $this->assertEquals('Almarai', $updatedConfig['font_family']);
        $this->assertEquals('Alexandria', $updatedConfig['font_heading_family']);
        $this->assertEquals('17px', $updatedConfig['font_size_base']);
        $this->assertEquals('800', $updatedConfig['font_weight_headings']);
    }

    public function test_storefront_renders_dynamic_typography_styles(): void
    {
        Setting::set('font_family', 'Readex Pro');
        Setting::set('font_heading_family', 'Outfit');

        $response = $this->get(route('customer.home'));

        $response->assertStatus(200);
        $response->assertSee('Readex+Pro');
        $response->assertSee('Outfit');
        $response->assertSee("--font-family-base: 'Readex Pro'", false);
        $response->assertSee("--font-family-headings: 'Outfit'", false);
    }

    public function test_filament_manage_typography_page_mounts_and_saves_settings(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin_typo@bluezone.com',
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Filament\Pages\ManageTypography::class)
            ->assertSet('font_family', 'Mont Blanc')
            ->set('font_family', 'Tajawal')
            ->set('font_heading_family', 'Alexandria')
            ->set('font_size_base', '18px')
            ->set('font_weight_headings', '900')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals('Tajawal', Setting::get('font_family'));
        $this->assertEquals('Alexandria', Setting::get('font_heading_family'));
        $this->assertEquals('18px', Setting::get('font_size_base'));
        $this->assertEquals('900', Setting::get('font_weight_headings'));
    }

    public function test_custom_admin_settings_updates_typography(): void
    {
        $role = \App\Models\Role::firstOrCreate(
            ['name' => 'Super Administrator'],
            ['slug' => 'super-admin', 'permissions' => ['*'], 'is_system' => true]
        );

        $admin = User::firstOrCreate(
            ['email' => 'custom_admin_typo@bluezone.com'],
            ['name' => 'Bluezone Admin', 'password' => bcrypt('password'), 'role_id' => $role->id, 'status' => 'active']
        );

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'tax_percentage' => 15,
            'tax_number' => '31004829100003',
            'font_family' => 'IBM Plex Sans Arabic',
            'font_heading_family' => 'IBM Plex Sans Arabic',
            'font_size_base' => '15px',
            'font_weight_headings' => '600',
            'font_weight_body' => '400',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertEquals('IBM Plex Sans Arabic', Setting::get('font_family'));
        $this->assertEquals('15px', Setting::get('font_size_base'));
        $this->assertEquals('600', Setting::get('font_weight_headings'));
    }
}
