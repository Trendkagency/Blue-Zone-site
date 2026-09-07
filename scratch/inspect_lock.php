<?php
$lock = json_decode(file_get_contents('composer.lock'), true);
echo "Packages count: " . count($lock['packages']) . PHP_EOL;
echo "Dev packages count: " . count($lock['packages-dev'] ?? []) . PHP_EOL;

echo "--- Root requirements in lock? ---" . PHP_EOL;
if (isset($lock['content-hash'])) {
    echo "Content hash: " . $lock['content-hash'] . PHP_EOL;
}

$find = ['laravel/framework', 'filament/filament', 'laravel/tinker', 'spatie/laravel-medialibrary', 'filament'];
foreach ($lock['packages'] as $p) {
    foreach ($find as $f) {
        if (str_contains($p['name'], $f)) {
            echo "Found in packages: " . $p['name'] . " => " . $p['version'] . PHP_EOL;
        }
    }
}
foreach ($lock['packages-dev'] ?? [] as $p) {
    foreach ($find as $f) {
        if (str_contains($p['name'], $f)) {
            echo "Found in packages-dev: " . $p['name'] . " => " . $p['version'] . PHP_EOL;
        }
    }
}
