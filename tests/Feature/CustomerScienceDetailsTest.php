<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerScienceDetailsTest extends TestCase
{
    use RefreshDatabase;

    protected Product $productA;
    protected Product $productB;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'name_en' => 'Cognitive & Longevity',
            'name_ar' => 'الإدراك وطول العمر',
            'slug' => 'cognitive-longevity',
            'is_active' => true,
        ]);

        $this->productA = Product::create([
            'category_id' => $category->id,
            'name_en' => 'BLUE MIND',
            'name_ar' => 'بلو مايند',
            'slug' => 'blue-mind',
            'sku' => 'BZ-MND-001',
            'price' => 68.00,
            'is_active' => true,
            'status' => 'active',
            'science_en' => 'Formulated from Bacopa Monnieri and Rhodiola to sustain synaptic neurotransmission.',
            'science_ar' => 'مركب نوتروبيك يعتمد على الباكوبا والروديولا لتعزيز المشابك العصبية.',
            'clinical_mechanism' => 'Choline donor phosphatidylcholine promotes synaptic vesicle docking.',
            'benefits_en' => ['8+ hours sustained focus', 'Promotes BDNF neurogenesis'],
            'benefits_ar' => ['تركيز ذهني متواصل 8+ ساعات', 'تحفيز عامل التغذية العصبية BDNF'],
            'usage_en' => 'Take 2 capsules every morning with cold-pressed olive oil.',
            'usage_ar' => 'تناول كبسولتين صباحاً مع زيت الزيتون البكر.',
            'stock_online' => 50,
            'stock_offline' => 20,
        ]);

        $this->productB = Product::create([
            'category_id' => $category->id,
            'name_en' => 'BLUE CELL',
            'name_ar' => 'بلو سيل',
            'slug' => 'blue-cell',
            'sku' => 'BZ-CLL-001',
            'price' => 74.00,
            'is_active' => true,
            'status' => 'active',
            'science_en' => 'Formulated to target mitochondrial ATP production and cellular NAD+ replenishment.',
            'science_ar' => 'مركب خلوي متطور يستهدف تجديد مخزون إنزيم NAD+ الخلوي وتنشيط الميتوكوندريا.',
            'clinical_mechanism' => 'Beta-NMN is converted into intracellular NAD+ to fuel sirtuins.',
            'benefits_en' => ['Mitochondrial respiration support', 'Accelerates cellular repair'],
            'benefits_ar' => ['دعم تنفس الميتوكوندريا الخلوية', 'تسريع آليات الترميم الخلوي'],
            'usage_en' => 'Take 2 capsules 20 minutes prior to morning nutrition.',
            'usage_ar' => 'تناول كبسولتين قبل وجبة الصباح بعشرين دقيقة.',
            'stock_online' => 40,
            'stock_offline' => 15,
        ]);
    }

    /**
     * Test that the products catalog page renders and contains "Our Science Details" button links.
     */
    public function test_products_catalog_page_contains_our_science_details_buttons(): void
    {
        $response = $this->get(route('customer.shop'));
        $response->assertStatus(200);
        $response->assertSee('Our Science Details');
        $response->assertSee('/our-science/' . $this->productA->slug);

        $productsResponse = $this->get(route('customer.products'));
        $productsResponse->assertStatus(200);
        $productsResponse->assertSee('Our Science Details');
        $productsResponse->assertSee('/our-science/' . $this->productB->slug);
    }

    /**
     * Test that a valid product's science details page loads successfully.
     */
    public function test_can_access_product_science_details_page(): void
    {
        $response = $this->get(route('customer.science.product', $this->productA->slug));
        $response->assertStatus(200);
        $response->assertSee($this->productA->name_en);
        $response->assertSee('SCIENTIFIC DOSSIER');
        $response->assertSee('BIOCHEMICAL PATHWAY');
        $response->assertSee($this->productA->science_en);
    }

    /**
     * Test that Product A displays its own science and never displays Product B's main science content.
     */
    public function test_product_a_never_displays_product_b_science(): void
    {
        // Check Product A's science details
        $responseA = $this->get(route('customer.science.product', $this->productA->slug));
        $responseA->assertStatus(200);
        $responseA->assertSee($this->productA->name_en);
        $responseA->assertSee($this->productA->science_en);
        $responseA->assertSee($this->productA->clinical_mechanism);
        // Product A main hero should NOT have Product B's science
        $responseA->assertDontSee($this->productB->science_en);

        // Check Product B's science details
        $responseB = $this->get(route('customer.science.product', $this->productB->slug));
        $responseB->assertStatus(200);
        $responseB->assertSee($this->productB->name_en);
        $responseB->assertSee($this->productB->science_en);
        $responseB->assertSee($this->productB->clinical_mechanism);
        // Product B main hero should NOT have Product A's science
        $responseB->assertDontSee($this->productA->science_en);
    }

    /**
     * Test that invalid product slug returns a clean 404 response.
     */
    public function test_invalid_product_returns_404_on_our_science_details(): void
    {
        $response = $this->get('/our-science/non-existent-product-xyz-999');
        $response->assertStatus(404);
    }

    /**
     * Test that an inactive product returns a 404 response.
     */
    public function test_inactive_product_returns_404_on_our_science_details(): void
    {
        $inactive = Product::create([
            'slug' => 'test-inactive-science-product',
            'sku' => 'BZ-INACT-001',
            'name_en' => 'Inactive Formulation',
            'name_ar' => 'تركيبة معطلة',
            'price' => 99.00,
            'is_active' => false,
            'status' => 'inactive',
        ]);

        $response = $this->get(route('customer.science.product', $inactive->slug));
        $response->assertStatus(404);
    }

    /**
     * Test that the /science/{slug} alias route also works seamlessly.
     */
    public function test_science_alias_route_works(): void
    {
        $response = $this->get('/science/' . $this->productA->slug);
        $response->assertStatus(200);
        $response->assertSee($this->productA->name_en);
        $response->assertSee($this->productA->science_en);
    }
}
