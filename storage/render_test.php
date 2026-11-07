<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $product = App\Models\Product::find(7) ?? App\Models\Product::first();
    echo "Found product: " . ($product ? $product->id : 'none') . "\n";
    $categories = App\Models\Category::all();
    $rawMaterials = App\Models\RawMaterial::all();
    $currencies = [];
    
    // Authenticate a user if needed for auth() checks in blade
    $user = App\Models\User::first();
    if ($user) auth()->login($user);

    $view = view('admin.products.edit', [
        'product' => $product,
        'categories' => $categories,
        'rawMaterials' => $rawMaterials,
        'currencies' => $currencies,
    ])->render();

    file_put_contents(__DIR__ . '/rendered_edit.html', $view);
    $lines = explode("\n", $view);
    echo "Rendered successfully, total lines: " . count($lines) . "\n";

    $targetLine = 8745;
    $start = max(0, $targetLine - 15);
    $end = min(count($lines) - 1, $targetLine + 15);
    echo "Lines around $targetLine:\n";
    for ($i = $start; $i <= $end; $i++) {
        echo ($i + 1) . ": " . $lines[$i] . "\n";
    }
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
