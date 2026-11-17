<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $html = view('admin.products.create', [
        'product' => new \App\Models\Product(),
        'categories' => [],
        'taxInfo' => [],
        'detailsSettings' => [],
        'allProducts' => collect(),
    ])->render();
    echo "SUCCESS: admin.products.create rendered " . strlen($html) . " bytes\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
}
