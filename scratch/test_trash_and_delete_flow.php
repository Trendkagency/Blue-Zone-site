<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Customer;

$initialReq = Request::create('http://blue_zone_site.test/admin');
$app->instance('request', $initialReq);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=========================================================\n";
echo "     COMPREHENSIVE TRASH & DELETE LIFECYCLE TEST SUITE    \n";
echo "=========================================================\n\n";

$admin = User::where('email', 'admin@bluezone.com')->first();
auth()->login($admin);

use Illuminate\Support\Str;

function runRequest($kernel, $user, $uri, $method = 'GET', $data = []) {
    auth()->login($user);
    
    $token = Str::random(40);
    $session = app('session.store');
    $session->start();
    $session->put('_token', $token);
    
    if (in_array(strtoupper($method), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
        $data['_token'] = $token;
    }
    
    $request = Request::create($uri, $method, $data);
    $request->setLaravelSession($session);
    $request->setUserResolver(fn() => $user);
    $request->headers->set('Accept', 'text/html,application/xhtml+xml');
    $request->headers->set('X-CSRF-TOKEN', $token);
    
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    $kernel->terminate($request, $response);
    return [$status, $response];
}

// ---------------------------------------------------------
// TEST 1: Products Trash & Delete Lifecycle
// ---------------------------------------------------------
echo "==> TEST 1: Products Trash & Delete Lifecycle\n";

// 1.1 Verify Trash view with 0 trashed items doesn't show fake items
$initialTrashedCount = Product::onlyTrashed()->count();
echo "- Initial Trashed Products Count: {$initialTrashedCount}\n";
[$status, $resp] = runRequest($kernel, $admin, '/admin/products?status=trashed');
echo "- GET /admin/products?status=trashed status: {$status}\n";

// 1.2 Create a temporary product for testing
$testProduct = Product::create([
    'slug' => 'test-lifecycle-product-' . time(),
    'name_en' => 'Test Lifecycle Product',
    'name_ar' => 'منتج اختبار دورة الحذف',
    'sku' => 'TEST-SKU-' . time(),
    'price' => 99.00,
    'cost_price' => 45.00,
    'stock_online' => 20,
    'stock_offline' => 10,
    'status' => 'active',
]);
echo "- Created Test Product ID: {$testProduct->id}\n";

// 1.3 Soft Delete
[$status, $resp] = runRequest($kernel, $admin, "/admin/products/{$testProduct->id}", 'DELETE');
echo "- DELETE /admin/products/{$testProduct->id} status: {$status} (Expect 302 redirect)\n";
$isTrashed = Product::onlyTrashed()->where('id', $testProduct->id)->exists();
echo "- Product is now in Trash: " . ($isTrashed ? "YES (PASS)" : "NO (FAIL)") . "\n";

// 1.4 Restore
[$status, $resp] = runRequest($kernel, $admin, "/admin/products/{$testProduct->id}/restore", 'POST');
echo "- POST /admin/products/{$testProduct->id}/restore status: {$status} (Expect 302 redirect)\n";
$isRestored = Product::where('id', $testProduct->id)->exists();
echo "- Product is now Restored to Active: " . ($isRestored ? "YES (PASS)" : "NO (FAIL)") . "\n";

// 1.5 Soft Delete again then Force Delete
runRequest($kernel, $admin, "/admin/products/{$testProduct->id}", 'DELETE');
[$status, $resp] = runRequest($kernel, $admin, "/admin/products/{$testProduct->id}/force-delete", 'DELETE');
echo "- DELETE /admin/products/{$testProduct->id}/force-delete status: {$status} (Expect 302 redirect)\n";
$isPermanentlyGone = !Product::withTrashed()->where('id', $testProduct->id)->exists();
echo "- Product is permanently deleted from DB: " . ($isPermanentlyGone ? "YES (PASS)" : "NO (FAIL)") . "\n";

// ---------------------------------------------------------
// TEST 2: Categories Trash & Delete Lifecycle
// ---------------------------------------------------------
echo "\n==> TEST 2: Categories Trash & Delete Lifecycle\n";
$testCat = Category::create([
    'name_en' => 'Test Lifecycle Category',
    'name_ar' => 'تصنيف اختبار دورة الحذف',
    'slug' => 'test-cat-' . time(),
    'is_active' => true,
]);
echo "- Created Test Category ID: {$testCat->id}\n";

// Soft Delete
[$status, $resp] = runRequest($kernel, $admin, "/admin/categories/{$testCat->id}", 'DELETE');
echo "- DELETE /admin/categories/{$testCat->id} status: {$status}\n";
$isCatTrashed = Category::onlyTrashed()->where('id', $testCat->id)->exists();
echo "- Category is now in Trash: " . ($isCatTrashed ? "YES (PASS)" : "NO (FAIL)") . "\n";

// Restore
[$status, $resp] = runRequest($kernel, $admin, "/admin/categories/{$testCat->id}/restore", 'POST');
echo "- POST /admin/categories/{$testCat->id}/restore status: {$status}\n";
$isCatRestored = Category::where('id', $testCat->id)->exists();
echo "- Category is Restored: " . ($isCatRestored ? "YES (PASS)" : "NO (FAIL)") . "\n";

// Force Delete
runRequest($kernel, $admin, "/admin/categories/{$testCat->id}", 'DELETE');
[$status, $resp] = runRequest($kernel, $admin, "/admin/categories/{$testCat->id}/force-delete", 'DELETE');
echo "- DELETE /admin/categories/{$testCat->id}/force-delete status: {$status}\n";
$isCatGone = !Category::withTrashed()->where('id', $testCat->id)->exists();
echo "- Category is permanently deleted: " . ($isCatGone ? "YES (PASS)" : "NO (FAIL)") . "\n";

// ---------------------------------------------------------
// TEST 3: Customers Trash & Delete Lifecycle
// ---------------------------------------------------------
echo "\n==> TEST 3: Customers Trash & Delete Lifecycle\n";
$testCust = Customer::create([
    'name' => 'Test Customer Delete',
    'email' => 'test_delete_' . time() . '@example.com',
    'status' => 'active',
]);
echo "- Created Test Customer ID: {$testCust->id}\n";

// Soft Delete
[$status, $resp] = runRequest($kernel, $admin, "/admin/customers/{$testCust->id}", 'DELETE');
echo "- DELETE /admin/customers/{$testCust->id} status: {$status}\n";
echo "- Customer in Trash: " . (Customer::onlyTrashed()->where('id', $testCust->id)->exists() ? "YES (PASS)" : "NO (FAIL)") . "\n";

// Force Delete
[$status, $resp] = runRequest($kernel, $admin, "/admin/customers/{$testCust->id}/force-delete", 'DELETE');
echo "- DELETE /admin/customers/{$testCust->id}/force-delete status: {$status}\n";
echo "- Customer permanently deleted: " . (!Customer::withTrashed()->where('id', $testCust->id)->exists() ? "YES (PASS)" : "NO (FAIL)") . "\n";

// ---------------------------------------------------------
// TEST 4: Users Trash & Delete Lifecycle (Self-protection & normal)
// ---------------------------------------------------------
echo "\n==> TEST 4: Users Trash & Delete Lifecycle\n";
$testUser = User::create([
    'name' => 'Test User Delete',
    'email' => 'test_user_del_' . time() . '@example.com',
    'password' => bcrypt('password123'),
    'role_id' => 3,
    'status' => 'active',
]);
echo "- Created Test User ID: {$testUser->id}\n";

// Soft Delete
[$status, $resp] = runRequest($kernel, $admin, "/admin/users/{$testUser->id}", 'DELETE');
echo "- DELETE /admin/users/{$testUser->id} status: {$status}\n";
echo "- User in Trash: " . (User::onlyTrashed()->where('id', $testUser->id)->exists() ? "YES (PASS)" : "NO (FAIL)") . "\n";

// Restore
[$status, $resp] = runRequest($kernel, $admin, "/admin/users/{$testUser->id}/restore", 'POST');
echo "- POST /admin/users/{$testUser->id}/restore status: {$status}\n";
echo "- User Restored: " . (User::where('id', $testUser->id)->exists() ? "YES (PASS)" : "NO (FAIL)") . "\n";

// Force Delete
runRequest($kernel, $admin, "/admin/users/{$testUser->id}", 'DELETE');
[$status, $resp] = runRequest($kernel, $admin, "/admin/users/{$testUser->id}/force-delete", 'DELETE');
echo "- DELETE /admin/users/{$testUser->id}/force-delete status: {$status}\n";
echo "- User permanently deleted: " . (!User::withTrashed()->where('id', $testUser->id)->exists() ? "YES (PASS)" : "NO (FAIL)") . "\n";

// Self Delete Protection
[$status, $resp] = runRequest($kernel, $admin, "/admin/users/{$admin->id}", 'DELETE');
echo "- Self-delete admin test status: {$status}\n";
echo "- Admin still exists: " . (User::where('id', $admin->id)->exists() ? "YES (PASS - Protected)" : "NO (FAIL)") . "\n";

// ---------------------------------------------------------
// TEST 5: Roles Protection Test
// ---------------------------------------------------------
echo "\n==> TEST 5: Roles Protection Test\n";
$superAdminRole = Role::find(1);
[$status, $resp] = runRequest($kernel, $admin, "/admin/roles/{$superAdminRole->id}", 'DELETE');
echo "- DELETE Super Admin Role status: {$status}\n";
echo "- Super Admin Role still exists: " . (Role::where('id', 1)->exists() ? "YES (PASS - Protected)" : "NO (FAIL)") . "\n";

echo "\n=========================================================\n";
echo "        ALL TRASH & DELETE FLOW TESTS COMPLETED!         \n";
echo "=========================================================\n";
