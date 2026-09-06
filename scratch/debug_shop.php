<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

try {
    $request = Illuminate\Http\Request::create('/products', 'GET');
    $response = $kernel->handle($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() !== 200) {
        $exception = $response->exception ?? null;
        if ($exception) {
            echo "Exception: " . $exception->getMessage() . "\n";
            echo "File: " . $exception->getFile() . " on line " . $exception->getLine() . "\n";
            echo "Trace:\n" . $exception->getTraceAsString() . "\n";
        } else {
            echo "Content snippet:\n" . substr($response->getContent(), 0, 500) . "\n";
        }
    }
} catch (\Throwable $e) {
    echo "Thrown Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
}
