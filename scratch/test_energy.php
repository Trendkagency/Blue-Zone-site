<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$uris = [
    '/our-science/blue-energy',
    '/products/blue-energy',
    '/our-science/blue-immunity',
    '/products/blue-immunity',
    '/our-science/blue-cell',
    '/our-science/blue-mind',
];

foreach ($uris as $uri) {
    $res = $kernel->handle(Illuminate\Http\Request::create($uri, 'GET'));
    echo "{$uri} => " . $res->getStatusCode() . "\n";
}
