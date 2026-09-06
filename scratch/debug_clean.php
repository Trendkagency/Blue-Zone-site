<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$uris = ['/products', '/products/blue-mind', '/our-science/blue-mind'];

foreach ($uris as $uri) {
    echo "\nTesting {$uri}...\n";
    $request = Illuminate\Http\Request::create($uri, 'GET');
    $response = $kernel->handle($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    if ($response->exception) {
        $e = $response->exception;
        echo "Exception: " . get_class($e) . ": " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    }
}
