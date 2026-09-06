<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$urls = [
    '/',
    '/science',
    '/products',
    '/products/blue-mind',
    '/our-science/blue-mind',
    '/shop',
    '/cart',
];

foreach ($urls as $url) {
    $request = Illuminate\Http\Request::create($url, 'GET');
    $response = $kernel->handle($request);
    echo "$url => Status: " . $response->getStatusCode() . "\n";
    $kernel->terminate($request, $response);
}
