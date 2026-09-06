<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductScienceTest extends TestCase
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
    }

    public function test_admin_create_page_contains_bilingual_science_fields_and_repeater(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.products.create'));

        $response->assertStatus(200);
        $response->assertSee('name="science_en"', false);
        $response->assertSee('name="science_ar"', false);
        $response->assertSee('name="clinical_mechanism"', false);
        $response->assertSee('name="formula_details"', false);
        $response->assertSee('name="benefits_en"', false);
        $response->assertSee('name="benefits_ar"', false);
        $response->assertSee('ingredientsTable', false);
        $response->assertSee('addIngredientRow', false);
    }

    public function test_admin_can_store_product_with_bilingual_science_and_ingredients(): void
    {
        $payload = [
            'sku' => 'BZ-SCI-001',
            'category_id' => $this->category->id,
            'brand' => 'Blue Zone Bioceuticals',
            'name_en' => 'Blue Mitochondria Core',
            'name_ar' => 'بلو ميتوكوندريا كور',
            'tagline_en' => 'Cellular ATP Surge',
            'tagline_ar' => 'تعزيز طاقة الميتوكوندريا الخلوية',
            'price' => 85.00,
            'cost_price' => 30.00,
            'stock_online' => 60,
            'stock_offline' => 25,
            'low_stock_threshold' => 10,
            'status' => 'active',
            'science_en' => 'Engineered to support electron transport chain Complex I through IV.',
            'science_ar' => 'مصمم لدعم سلسلة نقل الإلكترونات في الميتوكوندريا من المعقد الأول حتى الرابع.',
            'clinical_mechanism' => 'Rapidly phosphorylates cytosolic NMN into cellular NAD+ pools.',
            'formula_details' => '99.8% enzymatic purity via pharmaceutical extraction.',
            'benefits_en' => "+42% ATP output elevation in human trials\nShields inner mitochondrial membrane\nSustained physical endurance",
            'benefits_ar' => "+42% زيادة في إنتاج طاقة ATP في التجارب البشرية\nحماية الغشاء الداخلي للميتوكوندريا\nطاقة جسدية متواصلة",
            'ingredients' => [
                ['name_en' => 'Beta-NMN (Bioactive)', 'name_ar' => 'بيتا NMN النشط', 'dose' => '500 mg'],
                ['name_en' => 'Kaneka Ubiquinol CoQ10', 'name_ar' => 'يوبيكوينول كانيكا المنقى', 'dose' => '100 mg'],
            ],
            'contraindications' => 'Consult physician if undergoing active chemotherapy.',
            'warnings' => 'Keep below 25°C in sealed dark container.',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), $payload);

        $response->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'sku' => 'BZ-SCI-001',
            'name_en' => 'Blue Mitochondria Core',
            'science_en' => 'Engineered to support electron transport chain Complex I through IV.',
            'science_ar' => 'مصمم لدعم سلسلة نقل الإلكترونات في الميتوكوندريا من المعقد الأول حتى الرابع.',
            'clinical_mechanism' => 'Rapidly phosphorylates cytosolic NMN into cellular NAD+ pools.',
        ]);

        $product = Product::where('sku', 'BZ-SCI-001')->first();
        $this->assertNotNull($product);
        $this->assertIsArray($product->benefits_en);
        $this->assertCount(3, $product->benefits_en);
        $this->assertEquals('+42% ATP output elevation in human trials', $product->benefits_en[0]);

        $this->assertIsArray($product->ingredients);
        $this->assertCount(2, $product->ingredients);
        $this->assertEquals('Beta-NMN (Bioactive)', $product->ingredients[0]['name_en']);
        $this->assertEquals('500 mg', $product->ingredients[0]['dose']);
    }

    public function test_admin_edit_page_prepopulates_science_fields_and_ingredients(): void
    {
        $product = Product::create([
            'sku' => 'BZ-TEST-99',
            'slug' => 'blue-synapse-max',
            'category_id' => $this->category->id,
            'brand' => 'Blue Zone Lab',
            'name_en' => 'Blue Synapse Max',
            'name_ar' => 'بلو سينابس ماكس',
            'price' => 70.00,
            'cost_price' => 20.00,
            'stock_online' => 40,
            'stock_offline' => 15,
            'low_stock_threshold' => 5,
            'status' => 'active',
            'science_en' => 'Focuses on synaptic plasticity and memory recall.',
            'science_ar' => 'يركز على مرونة المشابك العصبية وسرعة الاستدعاء.',
            'clinical_mechanism' => 'Modulates acetylcholine neurotransmitter levels.',
            'formula_details' => 'Standardized Ginkgo biloba 24% flavone glycosides.',
            'benefits_en' => ['Memory retention +25%', 'Sustained cerebral focus'],
            'benefits_ar' => ['تحسين الذاكرة بنسبة +25%', 'تركيز ذهني متواصل'],
            'ingredients' => [
                ['name_en' => 'Phosphatidylserine', 'name_ar' => 'فوسفاتيديل سيرين', 'dose' => '150 mg'],
            ],
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.products.edit', $product->id));

        $response->assertStatus(200);
        $response->assertSee('Focuses on synaptic plasticity and memory recall.');
        $response->assertSee('يركز على مرونة المشابك العصبية وسرعة الاستدعاء.');
        $response->assertSee('Modulates acetylcholine neurotransmitter levels.');
        $response->assertSee('Memory retention +25%');
        $response->assertSee('Phosphatidylserine');
    }

    public function test_admin_can_update_product_science_fields(): void
    {
        $product = Product::create([
            'sku' => 'BZ-UPD-01',
            'slug' => 'original-formulation',
            'category_id' => $this->category->id,
            'brand' => 'Blue Zone Lab',
            'name_en' => 'Original Formulation',
            'name_ar' => 'التركيبة الأصلية',
            'price' => 50.00,
            'cost_price' => 15.00,
            'stock_online' => 20,
            'stock_offline' => 10,
            'low_stock_threshold' => 5,
            'status' => 'active',
            'science_en' => 'Initial science draft.',
        ]);

        $updateData = [
            'sku' => 'BZ-UPD-01',
            'category_id' => $this->category->id,
            'brand' => 'Blue Zone Lab Updated',
            'name_en' => 'Updated Science Formula',
            'name_ar' => 'التركيبة المحدثة',
            'price' => 55.00,
            'cost_price' => 18.00,
            'stock_online' => 30,
            'stock_offline' => 12,
            'low_stock_threshold' => 5,
            'status' => 'active',
            'science_en' => 'Fully updated molecular longevity dossier.',
            'science_ar' => 'ملف علمي متكامل محدث لطول العمر الخلوي.',
            'clinical_mechanism' => 'Upregulates sirtuin 1 (SIRT1) enzyme expression.',
            'benefits_en' => "Cellular rejuvenation\nExtended telomere protection",
            'benefits_ar' => "تجديد حيوي للخلية\nحماية نهايات التيلومير",
            'ingredients' => [
                ['name_en' => 'Resveratrol 98%', 'name_ar' => 'ريزفيراترول 98%', 'dose' => '250 mg'],
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product->id), $updateData);

        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertEquals('Fully updated molecular longevity dossier.', $product->science_en);
        $this->assertEquals('ملف علمي متكامل محدث لطول العمر الخلوي.', $product->science_ar);
        $this->assertEquals('Upregulates sirtuin 1 (SIRT1) enzyme expression.', $product->clinical_mechanism);
        $this->assertCount(2, $product->benefits_en);
        $this->assertEquals('Extended telomere protection', $product->benefits_en[1]);
        $this->assertCount(1, $product->ingredients);
        $this->assertEquals('Resveratrol 98%', $product->ingredients[0]['name_en']);
    }

    public function test_homepage_our_science_section_renders_dynamic_product_data(): void
    {
        Product::create([
            'sku' => 'BZ-HOMESCI-01',
            'category_id' => $this->category->id,
            'brand' => 'Blue Zone',
            'name_en' => 'Blue Quantum Shield',
            'name_ar' => 'بلو كوانتوم شيلد',
            'slug' => 'blue-quantum-shield',
            'price' => 90.00,
            'cost_price' => 35.00,
            'stock_online' => 50,
            'stock_offline' => 20,
            'low_stock_threshold' => 5,
            'status' => 'active',
            'is_active' => true,
            'science_en' => 'Revolutionary bio-photonic mitochondrial shield.',
            'clinical_mechanism' => 'Inhibits hydroxyl radicals and scavenges lipid peroxides.',
            'benefits_en' => ['+55% free radical scavenging capacity'],
            'ingredients' => [
                ['name_en' => 'Astaxanthin Complex', 'dose' => '12 mg'],
            ],
        ]);

        $response = $this->get(route('customer.home'));

        $response->assertStatus(200);
        $response->assertSee('Blue Quantum Shield');
        $response->assertSee('Revolutionary bio-photonic mitochondrial shield.');
        $response->assertSee('Astaxanthin Complex (12 mg)');
        $response->assertSee(route('customer.science.product', 'blue-quantum-shield'));
    }
}
