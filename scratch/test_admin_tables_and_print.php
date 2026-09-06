<?php

require __DIR__ . '/../vendor/autoload.php';

/** @var Illuminate\Foundation\Application $app */
$app = require __DIR__ . '/../bootstrap/app.php';

$app->boot();

$user = App\Models\User::where('email', 'admin@bluezone.com')->first();
if (!$user) {
    echo "Creating admin user for test...\n";
    $user = App\Models\User::create([
        'name' => 'Bluezone Executive Admin',
        'email' => 'admin@bluezone.com',
        'password' => bcrypt('password'),
    ]);
}

$role = Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
$user->assignRole($role);

$routes = [
    'Dashboard' => ['GET', '/admin/dashboard'],
    'Orders Index' => ['GET', '/admin/orders'],
    'Order Show' => ['GET', '/admin/orders/1'],
    'Invoices Index' => ['GET', '/admin/invoices'],
    'Invoice Show' => ['GET', '/admin/invoices/1'],
    'Invoice Print (Special Design)' => ['GET', '/admin/invoices/1/print'],
    'Inventory Index' => ['GET', '/admin/inventory'],
    'Inventory Transfers' => ['GET', '/admin/inventory/transfers'],
    'Inventory History' => ['GET', '/admin/inventory/history'],
    'Offline Sales Index' => ['GET', '/admin/offline-sales'],
    'Offline POS Create' => ['GET', '/admin/offline-sales/create'],
    'Customers CRM' => ['GET', '/admin/customers'],
    'Reports & Analytics' => ['GET', '/admin/reports'],
    'Products Index' => ['GET', '/admin/products'],
    'Categories Index' => ['GET', '/admin/categories'],
];

echo "=== TESTING ALL ADMIN PAGES & TABLES ===\n";

foreach ($routes as $name => [$method, $uri]) {
    $request = Illuminate\Http\Request::create($uri, $method);
    $request->setLaravelSession(app('session.store'));
    auth()->guard('web')->setUser($user);
    $response = $app->handleRequest($request);
    $status = $response->getStatusCode();
    
    if ($status === 200) {
        echo "[OK 200] {$name} -> {$uri}\n";
    } else {
        echo "[FAIL {$status}] {$name} -> {$uri}\n";
    }
}

echo "\n=== TESTING REAL-TIME ACTIONS & PRINT VIEW CONTENT ===\n";

// Test Status Update
$order = App\Models\Order::first();
if ($order) {
    $req = Illuminate\Http\Request::create("/admin/orders/{$order->id}/status", 'PATCH', ['status' => 'processing']);
    $req->setLaravelSession(app('session.store'));
    auth()->guard('web')->setUser($user);
    $res = $app->handleRequest($req);
    echo "Order Status Update Response: " . $res->getStatusCode() . " (Redirects: " . ($res->isRedirect() ? 'YES' : 'NO') . ")\n";
}

// Test Stock Transfer
$product = App\Models\Product::first();
if ($product) {
    $req = Illuminate\Http\Request::create('/admin/inventory/transfers', 'POST', [
        'product_id' => $product->id,
        'from_location' => 'online',
        'to_location' => 'offline',
        'quantity' => 2,
        'reason' => 'VIP Salon Replenishment',
    ]);
    $req->setLaravelSession(app('session.store'));
    auth()->guard('web')->setUser($user);
    $res = $app->handleRequest($req);
    echo "Stock Transfer Execution Response: " . $res->getStatusCode() . "\n";
}

// Test POS Checkout
if ($product) {
    $req = Illuminate\Http\Request::create('/admin/offline-sales', 'POST', [
        'customer_name' => 'VIP Walk-In Guest',
        'customer_phone' => '+966 55 123 4567',
        'payment_method' => 'Mada POS',
        'product_id' => $product->id,
        'quantity' => 1,
    ]);
    $req->setLaravelSession(app('session.store'));
    auth()->guard('web')->setUser($user);
    $res = $app->handleRequest($req);
    echo "POS Checkout Execution Response: " . $res->getStatusCode() . " (Target: " . $res->headers->get('Location') . ")\n";
}

// Test Print View Content
$printReq = Illuminate\Http\Request::create('/admin/invoices/1/print', 'GET');
$printReq->setLaravelSession(app('session.store'));
auth()->guard('web')->setUser($user);
$printRes = $app->handleRequest($printReq);
$content = $printRes->getContent();

$hasTaxNo = str_contains($content, '31004829100003');
$hasCR = str_contains($content, 'CR-1010842910');
$hasQR = str_contains($content, 'api.qrserver.com');
$hasPrintCss = str_contains($content, '@media print');

echo "\n=== PRINT VIEW VERIFICATION ===\n";
echo "Has Tax ID (31004829100003): " . ($hasTaxNo ? 'PASS' : 'FAIL') . "\n";
echo "Has CR Record (CR-1010842910): " . ($hasCR ? 'PASS' : 'FAIL') . "\n";
echo "Has ZATCA QR Verification: " . ($hasQR ? 'PASS' : 'FAIL') . "\n";
echo "Has Dedicated @media print CSS: " . ($hasPrintCss ? 'PASS' : 'FAIL') . "\n";
