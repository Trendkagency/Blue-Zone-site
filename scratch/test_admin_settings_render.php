<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
if ($user) {
    \Illuminate\Support\Facades\Auth::login($user);
}

$controller = new \App\Http\Controllers\Admin\SettingController();
$view = $controller->index();
$html = $view->render();

echo "Admin Settings Page rendered length: " . strlen($html) . " bytes\n";
assert(str_contains($html, 'landing_sections_sortable_container'), "Admin settings should render landing_sections_sortable_container");
assert(str_contains($html, 'data-section-key="hero_slider"'), "Admin settings should have hero_slider card");
assert(str_contains($html, 'data-section-key="our_science"'), "Admin settings should have our_science card");
assert(str_contains($html, 'landing_sections_order_input'), "Admin settings should have landing_sections_order_input");
echo ">>> ADMIN SETTINGS PAGE RENDERED AND VERIFIED PERFECTLY!\n";
