<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;
use App\View\ViewModels\SettingViewModel;
use Illuminate\Support\Facades\Request;

echo "========================================================\n";
echo "1. TESTING SETTINGVIEWMODEL AND DEFAULT SECTIONS\n";
echo "========================================================\n";
$meta = SettingViewModel::landingSections();
echo "Total sections defined: " . count($meta) . "\n";
assert(count($meta) === 12, "Expected 12 sections in SettingViewModel::landingSections");
foreach ($meta as $k => $info) {
    echo " - [$k]: {$info['name_en']} | {$info['name_ar']} (icon: {$info['icon']})\n";
}

echo "\n========================================================\n";
echo "2. TESTING HOMEPAGE RENDERING WITH DEFAULT SECTIONS\n";
echo "========================================================\n";
$homeController = new App\Http\Controllers\Customer\HomeController();
$response = $homeController->index();
$rendered = $response->render();

echo "Rendered homepage length: " . strlen($rendered) . " bytes\n";
assert(str_contains($rendered, 'hero-slider-container'), "Homepage should contain hero-slider-container");
assert(str_contains($rendered, 'who-we-are'), "Homepage should contain who-we-are");
assert(str_contains($rendered, 'philosophy'), "Homepage should contain philosophy");
assert(str_contains($rendered, 'new-arrivals'), "Homepage should contain new-arrivals");
assert(str_contains($rendered, 'featured-products'), "Homepage should contain featured-products");
assert(str_contains($rendered, 'products-vertical'), "Homepage should contain products-vertical");
assert(str_contains($rendered, 'blue-mind-flagship'), "Homepage should contain blue-mind-flagship");
assert(str_contains($rendered, 'five-blue-zones'), "Homepage should contain five-blue-zones");
assert(str_contains($rendered, 'bluemint-preps'), "Homepage should contain bluemint-preps");
assert(str_contains($rendered, 'our-science'), "Homepage should contain our-science");
assert(str_contains($rendered, 'blue-zone-journal'), "Homepage should contain blue-zone-journal");
assert(str_contains($rendered, 'final-cta'), "Homepage should contain final-cta");
echo ">>> ALL 12 SECTIONS RENDERED SUCCESSFULLY IN DEFAULT ORDER!\n";

echo "\n========================================================\n";
echo "3. TESTING MASTER ON/OFF TOGGLE (DISABLE WHO-WE-ARE & OUR-SCIENCE)\n";
echo "========================================================\n";
Setting::set('landing_who_we_are_enabled', false, 'landing', 'boolean');
Setting::set('landing_our_science_enabled', false, 'landing', 'boolean');
\Illuminate\Support\Facades\Cache::flush();

$responseDisabled = $homeController->index();
$renderedDisabled = $responseDisabled->render();

assert(!str_contains($renderedDisabled, 'id="who-we-are"'), "who-we-are should be hidden when disabled");
assert(!str_contains($renderedDisabled, 'id="our-science"'), "our-science should be hidden when disabled");
assert(str_contains($renderedDisabled, 'hero-slider-container'), "hero-slider-container should still be visible");
assert(str_contains($renderedDisabled, 'philosophy'), "philosophy should still be visible");
echo ">>> ON/OFF TOGGLE SUCCESSFULLY HIDDEN DISABLED SECTIONS!\n";

echo "\n========================================================\n";
echo "4. TESTING CUSTOM SECTION REORDERING (OUR-SCIENCE FIRST, HERO SECOND)\n";
echo "========================================================\n";
// Re-enable all
Setting::set('landing_who_we_are_enabled', true, 'landing', 'boolean');
Setting::set('landing_our_science_enabled', true, 'landing', 'boolean');

$newOrder = [
    'our_science',
    'hero_slider',
    'philosophy',
    'who_we_are',
    'new_arrivals',
    'featured_products',
    'products_vertical',
    'blue_mind_flagship',
    'five_blue_zones',
    'bluemint_preps',
    'journal_news',
    'final_cta'
];
Setting::set('landing_sections_order', $newOrder, 'landing', 'json');
\Illuminate\Support\Facades\Cache::flush();

$responseReordered = $homeController->index();
$renderedReordered = $responseReordered->render();

$posScience = strpos($renderedReordered, 'id="our-science"');
$posHero = strpos($renderedReordered, 'id="hero-slider-container"');

echo "Position of our-science: $posScience\n";
echo "Position of hero-slider: $posHero\n";
assert($posScience !== false && $posHero !== false && $posScience < $posHero, "our-science must appear before hero-slider in reordered output");
echo ">>> SECTION REORDERING VERIFIED: OUR-SCIENCE APPEARS BEFORE HERO!\n";

echo "\n========================================================\n";
echo "5. RESTORING DEFAULT SETTINGS FOR CLEAN STATE\n";
echo "========================================================\n";
$defaultOrder = array_keys(SettingViewModel::landingSections());
Setting::set('landing_sections_order', $defaultOrder, 'landing', 'json');
foreach ($defaultOrder as $k) {
    Setting::set("landing_{$k}_enabled", true, 'landing', 'boolean');
}
\Illuminate\Support\Facades\Cache::flush();
echo ">>> RESTORED DEFAULT STATE!\n";
echo "\nALL TESTS PASSED WITH 100% SUCCESS!\n";
