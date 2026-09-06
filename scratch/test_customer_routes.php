<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$routes = [
    '/' => 'Home Page',
    '/science' => 'Our Science Page',
    '/products' => 'Shop Catalog',
    '/products/blue-mind' => 'Product Detail (Blue Mind)',
    '/our-science/blue-mind' => 'Science Dossier (Blue Mind)',
    '/cart' => 'Cart Page',
    '/checkout' => 'Checkout Page',
    '/about' => 'About Page',
    '/contact' => 'Contact Page',
    '/faqs' => 'FAQs Page',
    '/terms' => 'Terms Page',
    '/privacy' => 'Privacy Page',
];

$allPassed = true;

foreach ($routes as $uri => $label) {
    $request = Illuminate\Http\Request::create($uri, 'GET');
    $response = $kernel->handle($request);
    $status = $response->getStatusCode();
    
    if ($status === 200) {
        echo " [OK 200] {$label} ({$uri})\n";
    } else {
        $allPassed = false;
        echo " [FAIL {$status}] {$label} ({$uri})\n";
        $content = $response->getContent();
        if (preg_match('/<title>(.*?)<\/title>/is', $content, $m)) {
            echo "   Title: " . trim($m[1]) . "\n";
        }
        if (preg_match('/class="exception_message">(.*?)<\/div>/is', $content, $m)) {
            echo "   Exception: " . trim(strip_tags($m[1])) . "\n";
        }
    }
}

if ($allPassed) {
    echo "\n>>> ALL CUSTOMER ROUTES TESTED & PASSED (200 OK) <<<\n";
} else {
    echo "\n>>> SOME ROUTES FAILED <<<\n";
}
