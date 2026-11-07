<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            ['name' => 'Super Admin', 'permissions' => ['*'], 'is_system' => true]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@bluezone.test'],
            ['name' => 'Admin Tariq', 'password' => bcrypt('secret'), 'role_id' => $role->id, 'status' => 'active']
        );

        $this->category = Category::firstOrCreate(
            ['slug' => 'cellular-test-cat'],
            ['name_en' => 'Cellular Longevity', 'name_ar' => 'صحة الخلايا']
        );
    }

    public function test_admin_can_instantly_delete_gallery_image_via_ajax(): void
    {
        Storage::fake('public');

        // Create test file in public uploads
        $destDir = public_path('storage/products/gallery');
        if (!file_exists($destDir)) {
            @mkdir($destDir, 0755, true);
        }
        $testFileName = 'test_to_delete_' . time() . '.png';
        $fullTestPath = $destDir . '/' . $testFileName;
        file_put_contents($fullTestPath, 'fake image data');

        $testUrl = '/storage/products/gallery/' . $testFileName;

        $product = Product::create([
            'sku' => 'BZ-DEL-TEST-01',
            'slug' => 'del-test-01',
            'category_id' => $this->category->id,
            'brand' => 'Blue Zone Bioceuticals',
            'name_en' => 'Delete Test Formulation',
            'name_ar' => 'مستحضر اختبار الحذف',
            'price' => 120.00,
            'cost_price' => 45.00,
            'stock_online' => 20,
            'images' => [
                $testUrl,
                '/storage/products/gallery/keep_this_one.png',
            ],
            'status' => 'active',
        ]);

        $this->assertTrue(file_exists($fullTestPath));

        $response = $this->actingAs($this->admin)->postJson("/admin/products/{$product->id}/delete-gallery-image", [
            'image_url' => $testUrl,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $product->refresh();
        $this->assertNotContains($testUrl, $product->images);
        $this->assertFalse(file_exists($fullTestPath));
    }

    public function test_admin_can_set_gallery_image_as_primary_hero(): void
    {
        $product = Product::create([
            'sku' => 'BZ-SET-HERO-01',
            'slug' => 'set-hero-01',
            'category_id' => $this->category->id,
            'brand' => 'Blue Zone Bioceuticals',
            'name_en' => 'Hero Test Formulation',
            'name_ar' => 'مستحضر اختبار الغلاف',
            'price' => 90.00,
            'cost_price' => 30.00,
            'stock_online' => 15,
            'image' => '/storage/products/hero/old_hero.jpg',
            'images' => ['/storage/products/gallery/new_hero_packaging.webp'],
            'status' => 'active',
        ]);

        $newHeroUrl = '/storage/products/gallery/new_hero_packaging.webp';

        $response = $this->actingAs($this->admin)->postJson("/admin/products/{$product->id}/set-primary-image", [
            'image_url' => $newHeroUrl,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'primary_image_url' => $newHeroUrl,
        ]);

        $product->refresh();
        $this->assertEquals($newHeroUrl, $product->image);
    }

    public function test_step_4_autosave_handles_gallery_upload_and_removal(): void
    {
        Storage::fake('public');

        $product = Product::create([
            'sku' => 'BZ-AUTOSAVE-STEP4',
            'slug' => 'autosave-step4',
            'category_id' => $this->category->id,
            'brand' => 'Blue Zone Bioceuticals',
            'name_en' => 'Step 4 Autosave Product',
            'name_ar' => 'منتج تجربة الخطوة 4',
            'price' => 85.00,
            'cost_price' => 25.00,
            'stock_online' => 10,
            'images' => ['/storage/products/gallery/image_to_remove.jpg'],
            'status' => 'active',
        ]);

        $newFile1 = UploadedFile::fake()->image('packaging_angle_1.webp');
        $newFile2 = UploadedFile::fake()->image('supplement_facts_2.png');

        $response = $this->actingAs($this->admin)->postJson("/admin/products/{$product->id}/auto-save-step", [
            'step' => 4,
            'gallery' => [$newFile1, $newFile2],
            'removed_gallery_images' => ['/storage/products/gallery/image_to_remove.jpg'],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'step' => 4,
        ]);

        $product->refresh();
        $this->assertNotContains('/storage/products/gallery/image_to_remove.jpg', $product->images);
        $this->assertCount(2, $product->images);
    }
}
