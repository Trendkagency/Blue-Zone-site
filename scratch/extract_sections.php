<?php

$indexFile = 'resources/views/customer/home/index.blade.php';
$content = file_get_contents($indexFile);
$lines = explode("\n", $content);

$partialsDir = 'resources/views/customer/home/partials';
if (!is_dir($partialsDir)) {
    mkdir($partialsDir, 0777, true);
}

// Slice boundaries (0-indexed line numbers)
$sections = [
    'hero_slider' => [13, 245],
    'who_we_are' => [246, 324],
    'philosophy' => [325, 598],
    'new_arrivals' => [599, 799],
    'featured_products' => [800, 830],
    'products_vertical' => [831, 975],
    'blue_mind_flagship' => [976, 1056],
    'five_blue_zones' => [1057, 1344],
    'bluemint_preps' => [1345, 1609],
    'our_science' => [1610, 1801],
    'journal_news' => [1802, 1872],
    'final_cta' => [1873, count($lines) - 1],
];

foreach ($sections as $key => [$start, $end]) {
    $slice = array_slice($lines, $start, ($end - $start + 1));
    $sectionContent = implode("\n", $slice);
    $target = "{$partialsDir}/{$key}.blade.php";
    file_put_contents($target, $sectionContent);
    echo "Created: {$target} (" . count($slice) . " lines)\n";
}

echo "All 12 partials created successfully!\n";
