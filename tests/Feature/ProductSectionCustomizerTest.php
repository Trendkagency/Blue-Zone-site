<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSectionCustomizerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(
            ['name' => 'Super Administrator'],
            ['slug' => 'super-admin', 'permissions' => ['*'], 'is_system' => true]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@bluezone.com'],
            ['name' => 'Bluezone Admin', 'password' => bcrypt('password'), 'role_id' => $role->id, 'status' => 'active']
        );

        $this->category = Category::firstOrCreate(
            ['slug' => 'longevity'],
            ['name_en' => 'Longevity', 'name_ar' => 'طول العمر', 'is_active' => true]
        );
    }

    public function test_product_model_returns_correct_default_section_configuration(): void
    {
        $product = new Product();
        $config = $product->getSectionConfig('sec1');

        $this->assertTrue($config['is_visible']);
        $this->assertTrue($config['enabled_desktop']);
        $this->assertTrue($config['enabled_mobile']);
        $this->assertTrue($config['default_open']); // sec1 opens by default
        $this->assertEquals('', $config['responsive_class']);
        $this->assertFalse($config['custom_content_enabled']);
        $this->assertNotEmpty($config['title']);
        $this->assertNotEmpty($config['badge']);
    }

    public function test_product_model_applies_custom_section_overrides_and_responsive_classes(): void
    {
        $product = new Product([
            'section_settings' => [
                'sec2' => [
                    'enabled_desktop' => true,
                    'enabled_mobile' => false,
                    'default_open' => true,
                    'title_en' => 'Custom Clinical Benefits',
                    'title_ar' => 'فوائد سريرية مخصصة',
                    'badge_en' => 'Biomarkers V2',
                    'badge_ar' => 'مؤشرات حيوية 2',
                    'custom_content_enabled' => true,
                    'custom_content_en' => '<p>Custom clinical HTML content</p>',
                    'custom_content_ar' => '<p>محتوى سريري مخصص</p>',
                ],
                'sec3' => [
                    'enabled_desktop' => false,
                    'enabled_mobile' => true,
                    'default_open' => false,
                ],
                'sec5' => [
                    'enabled_desktop' => false,
                    'enabled_mobile' => false,
                ],
            ],
        ]);

        // Test sec2: desktop only
        $sec2 = $product->getSectionConfig('sec2');
        $this->assertTrue($sec2['is_visible']);
        $this->assertTrue($sec2['enabled_desktop']);
        $this->assertFalse($sec2['enabled_mobile']);
        $this->assertEquals('hidden md:block', $sec2['responsive_class']);
        $this->assertTrue($sec2['default_open']);
        $this->assertTrue($sec2['custom_content_enabled']);
        $this->assertEquals('<p>Custom clinical HTML content</p>', $sec2['custom_content']);

        // Test sec3: mobile only
        $sec3 = $product->getSectionConfig('sec3');
        $this->assertTrue($sec3['is_visible']);
        $this->assertFalse($sec3['enabled_desktop']);
        $this->assertTrue($sec3['enabled_mobile']);
        $this->assertEquals('block md:hidden', $sec3['responsive_class']);
        $this->assertFalse($sec3['default_open']);

        // Test sec5: completely disabled
        $sec5 = $product->getSectionConfig('sec5');
        $this->assertFalse($sec5['is_visible']);
        $this->assertFalse($sec5['enabled_desktop']);
        $this->assertFalse($sec5['enabled_mobile']);
    }

    public function test_admin_create_and_edit_renders_step_6_section_customizer(): void
    {
        $product = Product::create([
            'sku' => 'TEST-CUST-001',
            'category_id' => $this->category->id,
            'brand' => 'Blue Zone',
            'name_en' => 'Customizer Test Formulation',
            'name_ar' => 'تركيبة تجربة التخصيص',
            'slug' => 'customizer-test-formulation',
            'price' => 120.00,
            'cost_price' => 60.00,
            'stock_online' => 50,
            'stock_offline' => 50,
            'low_stock_threshold' => 10,
            'status' => 'active',
        ]);

        // Check create page
        $createRes = $this->actingAs($this->admin, 'web')->get(route('admin.products.create'));
        $createRes->assertStatus(200);
        $createRes->assertSee('data-step="6"', false);
        $createRes->assertSee('goToStep(6)', false);
        $createRes->assertSee('section_settings[sec1][title_en]', false);

        // Check edit page
        $editRes = $this->actingAs($this->admin, 'web')->get(route('admin.products.edit', $product));
        $editRes->assertStatus(200);
        $editRes->assertSee('data-step="6"', false);
        $editRes->assertSee('goToStep(6)', false);
        $editRes->assertSee('section_settings[dossier][title_en]', false);
    }

    public function test_admin_can_save_and_update_section_settings(): void
    {
        $productData = [
            'sku' => 'TEST-SAVE-001',
            'category_id' => $this->category->id,
            'brand' => 'Blue Zone',
            'name_en' => 'Save Settings Test',
            'name_ar' => 'اختبار حفظ الإعدادات',
            'slug' => 'save-settings-test',
            'price' => 99.00,
            'cost_price' => 45.00,
            'stock_online' => 20,
            'stock_offline' => 10,
            'low_stock_threshold' => 5,
            'status' => 'active',
            'section_settings' => [
                'sec1' => [
                    'enabled_desktop' => 1,
                    'enabled_mobile' => 0,
                    'default_open' => 1,
                    'title_en' => 'Custom Ingredients Heading',
                    'title_ar' => 'عنوان المكونات المخصص',
                    'badge_en' => 'Purity Verified',
                    'badge_ar' => 'نقاء معتمد',
                ],
                'sec2' => [
                    'enabled_desktop' => 0,
                    'enabled_mobile' => 1,
                    'default_open' => 0,
                ],
            ],
        ];

        // Store
        $storeRes = $this->actingAs($this->admin, 'web')->post(route('admin.products.store'), $productData);
        $storeRes->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', 'TEST-SAVE-001')->firstOrFail();
        $this->assertIsArray($product->section_settings);
        $this->assertEquals('Custom Ingredients Heading', $product->section_settings['sec1']['title_en']);

        // Update
        $productData['section_settings']['sec1']['title_en'] = 'Updated Ingredients Heading';
        $updateRes = $this->actingAs($this->admin, 'web')->put(route('admin.products.update', $product), $productData);
        $updateRes->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertEquals('Updated Ingredients Heading', $product->section_settings['sec1']['title_en']);
    }

    public function test_customer_product_show_respects_custom_section_visibility_and_custom_content(): void
    {
        $product = Product::create([
            'sku' => 'TEST-SHOW-001',
            'category_id' => $this->category->id,
            'brand' => 'Blue Zone',
            'name_en' => 'Storefront Display Formulation',
            'name_ar' => 'تركيبة العرض المخصصة',
            'slug' => 'storefront-display-formulation',
            'price' => 150.00,
            'cost_price' => 70.00,
            'stock_online' => 30,
            'stock_offline' => 20,
            'low_stock_threshold' => 5,
            'status' => 'active',
            'section_settings' => [
                'sec1' => [
                    'enabled_desktop' => 1,
                    'enabled_mobile' => 1,
                    'default_open' => 1,
                    'title_en' => 'Custom Visible Active Compounds',
                    'custom_content_enabled' => 1,
                    'custom_content_en' => '<div id="unique-custom-content-marker">Bespoke HPLC Lab Assay Data</div>',
                ],
                'sec2' => [
                    'enabled_desktop' => 0,
                    'enabled_mobile' => 0, // Hidden completely
                    'title_en' => 'Hidden Clinical Benefits',
                ],
            ],
        ]);

        $response = $this->get(route('customer.product.show', $product->slug));
        $response->assertStatus(200);
        $response->assertSee('Custom Visible Active Compounds');
        $response->assertSee('unique-custom-content-marker');
        $response->assertDontSee('Hidden Clinical Benefits');
    }

    public function test_unchecking_desktop_and_mobile_persists_false_and_hides_section(): void
    {
        $product = Product::create([
            'sku' => 'TEST-UNCHECK-001',
            'category_id' => $this->category->id,
            'brand' => 'Blue Zone',
            'name_en' => 'Uncheck Visibility Test Formulation',
            'name_ar' => 'تركيبة اختبار إلغاء التحديد',
            'slug' => 'uncheck-visibility-test-formulation',
            'price' => 199.00,
            'cost_price' => 80.00,
            'stock_online' => 25,
            'stock_offline' => 15,
            'low_stock_threshold' => 5,
            'status' => 'active',
            'section_settings' => [
                'sec1' => [
                    'enabled_desktop' => 1,
                    'enabled_mobile' => 1,
                ],
                'buy_box' => [
                    'enabled_desktop' => 1,
                    'enabled_mobile' => 1,
                ],
            ],
        ]);

        // Verify initially visible
        $this->assertTrue($product->getSectionConfig('sec1')['is_visible']);
        $this->assertTrue($product->getSectionConfig('buy_box')['is_visible']);

        // Now update simulating form submission where checkboxes are unchecked (sending '0' via hidden input or omitted)
        $updatePayload = [
            'sku' => 'TEST-UNCHECK-001',
            'category_id' => $this->category->id,
            'brand' => 'Blue Zone',
            'name_en' => 'Uncheck Visibility Test Formulation',
            'name_ar' => 'تركيبة اختبار إلغاء التحديد',
            'slug' => 'uncheck-visibility-test-formulation',
            'price' => 199.00,
            'cost_price' => 80.00,
            'stock_online' => 25,
            'stock_offline' => 15,
            'low_stock_threshold' => 5,
            'status' => 'active',
            'section_settings' => [
                'sec1' => [
                    'enabled_desktop' => '0',
                    'enabled_mobile' => '0', // Unchecked both!
                    'title_en' => 'Unchecked Ingredients Section',
                ],
                'buy_box' => [
                    'enabled_desktop' => '1',
                    'enabled_mobile' => '0', // Desktop only
                ],
            ],
        ];

        $updateRes = $this->actingAs($this->admin, 'web')->put(route('admin.products.update', $product), $updatePayload);
        $updateRes->assertRedirect(route('admin.products.index'));

        $product->refresh();

        // 1. Assert sec1 is completely disabled
        $sec1Config = $product->getSectionConfig('sec1');
        $this->assertFalse($sec1Config['enabled_desktop']);
        $this->assertFalse($sec1Config['enabled_mobile']);
        $this->assertFalse($sec1Config['is_visible']);
        $this->assertEquals('hidden', $sec1Config['responsive_class']);

        // 2. Assert buy_box is enabled on desktop but hidden on mobile
        $buyBoxConfig = $product->getSectionConfig('buy_box');
        $this->assertTrue($buyBoxConfig['enabled_desktop']);
        $this->assertFalse($buyBoxConfig['enabled_mobile']);
        $this->assertTrue($buyBoxConfig['is_visible']);
        $this->assertEquals('hidden md:block', $buyBoxConfig['responsive_class']);

        // 3. Storefront rendering test
        $response = $this->get(route('customer.product.show', $product->slug));
        $response->assertStatus(200);
        // sec1 should be hidden or not seen
        $response->assertDontSee('Unchecked Ingredients Section');
    }
}
