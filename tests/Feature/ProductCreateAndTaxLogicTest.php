<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Services\TaxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCreateAndTaxLogicTest extends TestCase
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
            ['slug' => 'cellular-longevity'],
            ['name_en' => 'Cellular Longevity', 'name_ar' => 'طول العمر الخلوي', 'is_active' => true]
        );

        // Seed default tax settings
        Setting::set('tax_percentage', 15.0, 'tax', 'float');
        Setting::set('tax_number', '31004829100003', 'tax', 'string');
        Setting::set('enable_tax', true, 'tax', 'boolean');
        Setting::set('prices_include_tax', false, 'tax', 'boolean');
    }

    public function test_product_create_page_renders_with_steps_and_tax_breakdown(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.products.create'));
        $response->assertStatus(200);
        $response->assertSee('data-step="1"', false);
        $response->assertSee('data-step="6"', false);
        $response->assertSee('VAT 15%');
        $response->assertSee('recalculateTaxAndMargin', false);
    }

    public function test_product_store_validation_fails_when_mandatory_step_fields_are_missing(): void
    {
        $response = $this->actingAs($this->admin, 'web')->post(route('admin.products.store'), []);
        $response->assertSessionHasErrors(['sku', 'category_id', 'brand', 'name_en', 'name_ar', 'description_en', 'description_ar', 'price', 'cost_price', 'stock_online', 'stock_offline', 'low_stock_threshold', 'status']);
    }

    public function test_product_store_successfully_creates_record_and_inventory_allocation(): void
    {
        $payload = [
            'sku' => 'BZ-CEL-999',
            'barcode' => '628100999001',
            'category_id' => $this->category->id,
            'subcategory_en' => 'Mitochondrial Repair',
            'brand' => 'Blue Zone Bioceuticals',
            'target_gender' => 'Unisex',
            'age_group' => '25+',
            'product_size' => '60 Veggie Capsules',
            'slug' => 'blue-cell-atp-renew',
            'name_en' => 'BLUE CELL ATP Renew',
            'name_ar' => 'بلو سيل تجديد طاقة الميتوكوندريا',
            'tagline_en' => 'Cellular Co-Factors & ATP Energy',
            'tagline_ar' => 'عوامل مساعدة لتوليد طاقة الخلايا',
            'description_en' => 'Deep cellular formulation designed to support mitochondrial biogenesis and healthy cellular aging.',
            'description_ar' => 'تركيبة خلوية عميقة مصممة لدعم تجدد الميتوكوندريا والشيخوخة الحيوية الصحية.',
            'usage_en' => 'Take 2 capsules daily with morning food.',
            'usage_ar' => 'تناول كبسولتين يومياً مع وجبة الصباح.',
            'price' => 84.00,
            'sale_price' => 74.00,
            'cost_price' => 26.00,
            'image' => 'assets/products/blue-cell.jpg',
            'clinical_mechanism' => 'Activates AMPK and sirtuin longevity pathways.',
            'formula_details' => '99.8% pure stabilized NADH and CoQ10.',
            'stock_online' => 120,
            'stock_offline' => 60,
            'low_stock_threshold' => 15,
            'status' => 'active',
            'is_featured' => 1,
            'enable_backorders' => 0,
        ];

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.products.store'), $payload);
        $response->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'sku' => 'BZ-CEL-999',
            'name_en' => 'BLUE CELL ATP Renew',
            'price' => 84.00,
        ]);

        $product = Product::where('sku', 'BZ-CEL-999')->first();
        $this->assertNotNull($product);

        // Verify inventory provisioning
        $this->assertDatabaseHas('inventory_items', [
            'product_id' => $product->id,
            'location_id' => 'online',
            'available_stock' => 120,
        ]);
        $this->assertDatabaseHas('inventory_items', [
            'product_id' => $product->id,
            'location_id' => 'offline',
            'available_stock' => 60,
        ]);
    }

    public function test_product_update_validates_and_persists_modifications(): void
    {
        $product = Product::create([
            'sku' => 'BZ-TEST-001',
            'slug' => 'test-product-en',
            'category_id' => $this->category->id,
            'brand' => 'Blue Zone',
            'name_en' => 'Test Product EN',
            'name_ar' => 'منتج تجريبي',
            'description_en' => 'Test desc EN',
            'description_ar' => 'وصف تجريبي',
            'price' => 50.00,
            'cost_price' => 15.00,
            'stock_online' => 30,
            'stock_offline' => 20,
            'low_stock_threshold' => 5,
            'status' => 'active',
        ]);

        $updatePayload = [
            'sku' => 'BZ-TEST-001', // Should not trigger unique constraint error on self
            'category_id' => $this->category->id,
            'brand' => 'Blue Zone Bioceuticals Lab',
            'name_en' => 'Updated Test Product EN',
            'name_ar' => 'منتج تجريبي معدل',
            'description_en' => 'Updated description content',
            'description_ar' => 'وصف تفصيلي معدل بالكامل',
            'price' => 58.00,
            'cost_price' => 18.00,
            'stock_online' => 45,
            'stock_offline' => 25,
            'low_stock_threshold' => 8,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->admin, 'web')->put(route('admin.products.update', $product->id), $updatePayload);
        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertEquals(58.00, (float) $product->price);
        $this->assertEquals('Updated Test Product EN', $product->name_en);
    }

    public function test_tax_service_computes_dynamic_vat_breakdowns_and_order_totals(): void
    {
        // 1. Tax Exclusive (15% added on top of $100)
        Setting::set('tax_percentage', 15.0, 'tax', 'float');
        Setting::set('prices_include_tax', false, 'tax', 'boolean');
        Setting::set('enable_tax', true, 'tax', 'boolean');

        $breakdown = TaxService::breakdownPrice(100.00, 30.00);
        $this->assertEquals(100.00, $breakdown['net_price']);
        $this->assertEquals(15.00, $breakdown['tax_amount']);
        $this->assertEquals(115.00, $breakdown['gross_price']);
        $this->assertEquals(70.00, $breakdown['profit_margin']);

        // 2. Order Totals with shipping & discount
        $orderTotals = TaxService::calculateOrderTotals(200.00, 20.00, 10.00);
        $this->assertEquals(180.00, $orderTotals['taxable_amount']); // 200 - 20
        $this->assertEquals(27.00, $orderTotals['tax_amount']);       // 180 * 15%
        $this->assertEquals(217.00, $orderTotals['grand_total']);    // 180 + 27 + 10

        // 3. Tax Inclusive calculation
        Setting::set('prices_include_tax', true, 'tax', 'boolean');
        $incBreakdown = TaxService::breakdownPrice(115.00, 30.00);
        $this->assertEquals(100.00, $incBreakdown['net_price']);
        $this->assertEquals(15.00, $incBreakdown['tax_amount']);
        $this->assertEquals(115.00, $incBreakdown['gross_price']);
    }

    public function test_admin_dashboard_settings_controls_taxes_in_real_time(): void
    {
        $response = $this->actingAs($this->admin, 'web')->post(route('admin.settings.update'), [
            'tax_percentage' => 18.5,
            'tax_number' => '39998887770003',
            'enable_tax' => 1,
            'prices_include_tax' => 0,
            'site_name' => 'BLUE ZONE International',
            'low_stock_threshold' => 12,
            'toast_sound_enabled' => 1,
        ]);

        $response->assertRedirect(route('admin.settings.index'));

        $this->assertEquals(18.5, TaxService::getTaxRate());
        $this->assertEquals('39998887770003', TaxService::getTaxNumber());
        $this->assertEquals('BLUE ZONE International', Setting::get('site_name'));
        $this->assertTrue(Setting::get('toast_sound_enabled'));
    }
}
