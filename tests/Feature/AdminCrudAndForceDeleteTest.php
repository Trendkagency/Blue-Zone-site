<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCrudAndForceDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Role $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create([
            'name' => 'Super Admin',
            'description' => 'Super Administrator with full access',
            'permissions' => ['*'],
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active',
        ]);
    }

    public function test_product_crud_soft_delete_restore_and_force_delete(): void
    {
        $category = Category::create([
            'name_en' => 'Cellular Longevity',
            'name_ar' => 'طول العمر الخلوي',
            'slug' => 'cellular-longevity',
            'is_active' => true,
        ]);

        // 1. Create product
        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'sku' => 'BZ-CELL-001',
            'brand' => 'Blue Zone Bioceuticals',
            'name_en' => 'Blue Cell Longevity Formula',
            'name_ar' => 'تركيبة بلو سيل لطول العمر',
            'description_en' => 'Cellular rejuvenation and mitochondrial optimization protocol.',
            'description_ar' => 'بروتوكول تجديد الخلايا ودعم الميتوكوندريا.',
            'category_id' => $category->id,
            'price' => 78.00,
            'cost_price' => 24.00,
            'stock_online' => 100,
            'stock_offline' => 50,
            'low_stock_threshold' => 15,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $product = Product::where('slug', 'blue-cell-longevity-formula')->first();
        $this->assertNotNull($product);

        // 2. Update product
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.products.update', $product->id), [
            'sku' => 'BZ-CELL-001',
            'brand' => 'Blue Zone Bioceuticals',
            'name_en' => 'Blue Cell Longevity Protocol V2',
            'name_ar' => 'بروتوكول بلو سيل المطور',
            'description_en' => 'Cellular rejuvenation and mitochondrial optimization protocol updated.',
            'description_ar' => 'بروتوكول تجديد الخلايا ودعم الميتوكوندريا محدث.',
            'category_id' => $category->id,
            'price' => 84.00,
            'cost_price' => 26.00,
            'stock_online' => 120,
            'stock_offline' => 60,
            'low_stock_threshold' => 20,
            'status' => 'active',
        ]);

        $updateResponse->assertRedirect(route('admin.products.index'));
        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name_en' => 'Blue Cell Longevity Protocol V2']);

        // 3. Soft Delete product
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product->id));
        $deleteResponse->assertRedirect(route('admin.products.index'));
        $deleteResponse->assertSessionHas('success');

        $this->assertSoftDeleted('products', ['id' => $product->id]);

        // 4. Restore product
        $restoreResponse = $this->actingAs($this->admin)->post(route('admin.products.restore', $product->id));
        $restoreResponse->assertRedirect(route('admin.products.index', ['status' => 'trashed']));
        $restoreResponse->assertSessionHas('success');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'deleted_at' => null]);

        // 5. Force Delete product
        $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product->id));
        $forceDeleteResponse = $this->actingAs($this->admin)->delete(route('admin.products.force-delete', $product->id));
        $forceDeleteResponse->assertRedirect(route('admin.products.index', ['status' => 'trashed']));
        $forceDeleteResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_category_crud_soft_delete_restore_and_force_delete(): void
    {
        // 1. Create Category
        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name_en' => 'Neuro Synaptic Health',
            'name_ar' => 'صحة المشابك العصبية',
            'slug' => 'neuro-synaptic',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $category = Category::where('slug', 'neuro-synaptic')->first();
        $this->assertNotNull($category);

        // 2. Update Category
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.categories.update', $category->id), [
            'name_en' => 'Neuro Synaptic V2',
            'name_ar' => 'صحة المشابك العصبية المطورة',
            'slug' => 'neuro-synaptic',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        $updateResponse->assertRedirect(route('admin.categories.index'));
        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name_en' => 'Neuro Synaptic V2']);

        // 3. Soft Delete Category
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $category->id));
        $deleteResponse->assertRedirect(route('admin.categories.index'));
        $deleteResponse->assertSessionHas('success');
        $this->assertSoftDeleted('categories', ['id' => $category->id]);

        // 4. Restore Category
        $restoreResponse = $this->actingAs($this->admin)->post(route('admin.categories.restore', $category->id));
        $restoreResponse->assertRedirect(route('admin.categories.index', ['status' => 'trashed']));
        $restoreResponse->assertSessionHas('success');
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'deleted_at' => null]);

        // 5. Force Delete Category
        $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $category->id));
        $forceDeleteResponse = $this->actingAs($this->admin)->delete(route('admin.categories.force-delete', $category->id));
        $forceDeleteResponse->assertRedirect(route('admin.categories.index', ['status' => 'trashed']));
        $forceDeleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_user_crud_soft_delete_restore_and_force_delete(): void
    {
        $role = Role::create([
            'name' => 'Fulfillment Specialist',
            'description' => 'Handles logistics orders',
            'permissions' => ['orders.view', 'inventory.view'],
        ]);

        // 1. Create User
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'name' => 'Faisal Al-Otaibi',
            'email' => 'faisal.test@bluezone.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $user = User::where('email', 'faisal.test@bluezone.com')->first();
        $this->assertNotNull($user);

        // 2. Update User
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.users.update', $user->id), [
            'name' => 'Faisal Al-Otaibi Lead',
            'email' => 'faisal.test@bluezone.com',
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $updateResponse->assertRedirect(route('admin.users.index'));
        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Faisal Al-Otaibi Lead']);

        // 3. Soft Delete User
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $user->id));
        $deleteResponse->assertRedirect(route('admin.users.index'));
        $deleteResponse->assertSessionHas('success');
        $this->assertSoftDeleted('users', ['id' => $user->id]);

        // 4. Restore User
        $restoreResponse = $this->actingAs($this->admin)->post(route('admin.users.restore', $user->id));
        $restoreResponse->assertRedirect(route('admin.users.index', ['status' => 'trashed']));
        $restoreResponse->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);

        // 5. Force Delete User
        $this->actingAs($this->admin)->delete(route('admin.users.destroy', $user->id));
        $forceDeleteResponse = $this->actingAs($this->admin)->delete(route('admin.users.force-delete', $user->id));
        $forceDeleteResponse->assertRedirect(route('admin.users.index', ['status' => 'trashed']));
        $forceDeleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_customer_crud_soft_delete_restore_and_force_delete(): void
    {
        // 1. Create Customer
        $response = $this->actingAs($this->admin)->post(route('admin.customers.store'), [
            'name' => 'Dr. Khalid Al-Sulaiman',
            'email' => 'khalid.vip@example.com',
            'phone' => '+966 50 999 8877',
            'city' => 'Riyadh',
            'country' => 'Saudi Arabia',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.customers.index'));
        $response->assertSessionHas('success');

        $customer = Customer::where('email', 'khalid.vip@example.com')->first();
        $this->assertNotNull($customer);

        // 2. Update Customer
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.customers.update', $customer->id), [
            'name' => 'Dr. Khalid Al-Sulaiman (VIP)',
            'email' => 'khalid.vip@example.com',
            'phone' => '+966 50 999 8877',
            'city' => 'Jeddah',
            'country' => 'Saudi Arabia',
            'status' => 'active',
        ]);

        $updateResponse->assertRedirect(route('admin.customers.index'));
        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'city' => 'Jeddah']);

        // 3. Soft Delete Customer
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.customers.destroy', $customer->id));
        $deleteResponse->assertRedirect(route('admin.customers.index'));
        $deleteResponse->assertSessionHas('success');
        $this->assertSoftDeleted('customers', ['id' => $customer->id]);

        // 4. Restore Customer
        $restoreResponse = $this->actingAs($this->admin)->post(route('admin.customers.restore', $customer->id));
        $restoreResponse->assertRedirect(route('admin.customers.index', ['status' => 'trashed']));
        $restoreResponse->assertSessionHas('success');
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'deleted_at' => null]);

        // 5. Force Delete Customer
        $this->actingAs($this->admin)->delete(route('admin.customers.destroy', $customer->id));
        $forceDeleteResponse = $this->actingAs($this->admin)->delete(route('admin.customers.force-delete', $customer->id));
        $forceDeleteResponse->assertRedirect(route('admin.customers.index', ['status' => 'trashed']));
        $forceDeleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    public function test_faq_crud_operations(): void
    {
        // 1. Store FAQ
        $response = $this->actingAs($this->admin)->post(route('admin.content.faqs.store'), [
            'q_en' => 'What is the optimal bioavailability protocol?',
            'q_ar' => 'ما هو بروتوكول الامتصاص الحيوي الأمثل؟',
            'a_en' => 'Take with morning lipid-containing meals for maximum cellular uptake.',
            'a_ar' => 'تناول التركيبة مع وجبة الصباح المحتوية على دهون صحية لتحقيق أعلى معدل امتصاص.',
        ]);

        $response->assertRedirect(route('admin.content.faqs'));
        $response->assertSessionHas('success');

        $faqs = Setting::get('cms_faqs', []);
        $this->assertNotEmpty($faqs);

        // 2. Delete FAQ
        $lastIndex = count($faqs) - 1;
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.content.faqs.destroy', $lastIndex));
        $deleteResponse->assertRedirect(route('admin.content.faqs'));
        $deleteResponse->assertSessionHas('success');
    }
}
