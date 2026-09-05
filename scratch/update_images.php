<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$map = [
    'blue-mind' => '/assets/products/blue-mind.jpg',
    'blue-cell' => '/assets/products/blue-cell.jpg',
    'blue-defense' => '/assets/products/blue-defense.jpg',
    'blue-metabolic' => '/assets/products/blue-metabolic.jpg',
    'blue-sleep' => '/assets/products/blue-sleep.jpg',
    'blue-vitality' => '/assets/products/blue-vitality.jpg',
];

foreach (Product::all() as $p) {
    if (isset($map[$p->slug])) {
        $p->image = $map[$p->slug];
        $p->images = [$map[$p->slug]];
        $p->save();
        echo "Updated {$p->slug} -> {$p->image}\n";
    }
}

echo "Done updating product images!\n";
