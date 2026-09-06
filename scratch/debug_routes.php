<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$urls = ['/science', '/products/blue-mind', '/shop'];

foreach ($urls as $u) {
    echo "=== Testing $u ===\n";
    try {
        $req = Illuminate\Http\Request::create($u, 'GET');
        $res = $kernel->handle($req);
        if ($res->getStatusCode() >= 400) {
            echo "Status: " . $res->getStatusCode() . "\n";
            if (isset($res->exception)) {
                echo "Exception: " . get_class($res->exception) . ": " . $res->exception->getMessage() . "\n";
                echo "In " . $res->exception->getFile() . ":" . $res->exception->getLine() . "\n";
                echo $res->exception->getTraceAsString() . "\n";
            } else {
                echo substr($res->getContent(), 0, 500) . "\n";
            }
        } else {
            echo "OK (200)\n";
        }
    } catch (\Throwable $e) {
        echo "Direct Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}
