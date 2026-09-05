<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

app()->setLocale('ar');
$view = view('admin.products.create', ['categories' => []])->render();

echo "Tab core: " . (str_contains($view, '1. التصنيف والمعرفات الأساسية') ? 'YES' : 'NO') . "\n";
echo "Section core: " . (str_contains($view, 'المعرفات والتصنيف الأساسي') ? 'YES' : 'NO') . "\n";
echo "Field SKU: " . (str_contains($view, 'رمز المنتج (SKU)') ? 'YES' : 'NO') . "\n";
echo "Placeholder: " . (str_contains($view, 'مثال: BZ-MND-001') ? 'YES' : 'NO') . "\n";
echo "Subtitle: " . (str_contains($view, 'تسجيل تركيبة حيوية جديدة في كتالوج المنتجات المركزي') ? 'YES' : 'NO') . "\n";
