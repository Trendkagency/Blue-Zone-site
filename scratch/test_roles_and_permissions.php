<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

$initialReq = Request::create('http://blue_zone_site.test/admin');
$app->instance('request', $initialReq);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=========================================================\n";
echo "       RBAC & PERMISSIONS COMPREHENSIVE TEST SUITE       \n";
echo "=========================================================\n\n";

// 1. Inspect All Roles & Users
$roles = Role::all();
echo "==> Registered Roles in Database:\n";
foreach ($roles as $r) {
    echo "ID: {$r->id} | Name: {$r->name} | Permissions Count/Keys: " . (is_array($r->permissions) ? implode(', ', array_keys($r->permissions)) : $r->permissions) . "\n";
}

$users = User::with('role')->get();
echo "\n==> Registered Test Users:\n";
foreach ($users as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | Role: " . ($u->role->name ?? 'None') . "\n";
}

echo "\n---------------------------------------------------------\n";
echo "TEST 1: User::hasPermission() Direct Logic Evaluation\n";
echo "---------------------------------------------------------\n";

$salesUser = User::where('email', 'khalid@bluezone.com')->first();
$inventoryUser = User::where('email', 'omar@bluezone.com')->first();
$adminUser = User::where('email', 'admin@bluezone.com')->first();

if (!$salesUser) {
    echo "[!] Khalid not found, creating or checking another sales staff...\n";
}

echo "Testing Super Admin Permissions:\n";
echo "- hasPermission('products.view'): " . ($adminUser->hasPermission('products.view') ? "PASS (true)" : "FAIL (false)") . "\n";
echo "- hasPermission('users.delete'): " . ($adminUser->hasPermission('users.delete') ? "PASS (true)" : "FAIL (false)") . "\n";
echo "- hasPermission('anything.arbitrary'): " . ($adminUser->hasPermission('anything.arbitrary') ? "PASS (true)" : "FAIL (false)") . "\n";

echo "\nTesting Sales Staff Permissions (Role: Sales Staff with products.view):\n";
echo "- hasPermission('products.view'): " . ($salesUser->hasPermission('products.view') ? "PASS (true)" : "FAIL (false)") . "\n";
echo "- hasPermission('products'): " . ($salesUser->hasPermission('products') ? "PASS (true)" : "FAIL (false)") . "\n";
echo "- hasPermission('products.create'): " . (!$salesUser->hasPermission('products.create') ? "PASS (false)" : "FAIL (true)") . "\n";
echo "- hasPermission('users.view'): " . (!$salesUser->hasPermission('users.view') ? "PASS (false)" : "FAIL (true)") . "\n";
echo "- hasPermission('settings.view'): " . (!$salesUser->hasPermission('settings.view') ? "PASS (false)" : "FAIL (true)") . "\n";

echo "\n---------------------------------------------------------\n";
echo "TEST 2: HTTP Route Middleware End-to-End Simulation\n";
echo "---------------------------------------------------------\n";

function testRouteAsUser($app, $kernel, $user, $uri, $method = 'GET') {
    auth()->login($user);
    $request = Request::create($uri, $method);
    $request->setUserResolver(fn() => $user);
    
    try {
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        $kernel->terminate($request, $response);
        return $status;
    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        return 403;
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        return $e->getStatusCode();
    } catch (\Throwable $e) {
        return "ERROR: " . $e->getMessage();
    }
}

// Routes to test
$testCases = [
    // [Route, Expected Sales Staff Status, Expected Admin Status]
    ['/admin/products', 200, 200],
    ['/admin/products/create', 403, 200],
    ['/admin/categories', 200, 200],
    ['/admin/users', 403, 200],
    ['/admin/roles', 403, 200],
    ['/admin/settings', 403, 200],
    ['/admin/inventory', 403, 200],
];

echo "Simulating Requests for Sales Staff ({$salesUser->email}):\n";
foreach ($testCases as [$uri, $expectedSalesStatus, $expectedAdminStatus]) {
    $status = testRouteAsUser($app, $kernel, $salesUser, $uri);
    $result = ($status === $expectedSalesStatus) ? "PASS" : "FAIL (Got $status, Expected $expectedSalesStatus)";
    echo "  {$uri} => HTTP {$status} [{$result}]\n";
}

echo "\nSimulating Requests for Super Admin ({$adminUser->email}):\n";
foreach ($testCases as [$uri, $expectedSalesStatus, $expectedAdminStatus]) {
    $status = testRouteAsUser($app, $kernel, $adminUser, $uri);
    $result = ($status === $expectedAdminStatus) ? "PASS" : "FAIL (Got $status, Expected $expectedAdminStatus)";
    echo "  {$uri} => HTTP {$status} [{$result}]\n";
}

echo "\n=========================================================\n";
echo "                 ALL RBAC TESTS COMPLETED                 \n";
echo "=========================================================\n";
