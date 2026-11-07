<?php
require __DIR__ . '/vendor/autoload.php';
echo "Autoload done\n";
$app = require_once __DIR__ . '/bootstrap/app.php';
echo "App configured\n";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
echo "Kernel made\n";
$kernel->bootstrap();
echo "Kernel bootstrapped\n";

$product = App\Models\Product::find(7) ?? App\Models\Product::first();
echo "Product ID: " . $product->id . "\n";
echo "Done\n";
