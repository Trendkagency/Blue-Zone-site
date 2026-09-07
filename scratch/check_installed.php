<?php
if (!file_exists('vendor/composer/installed.json')) {
    echo "installed.json not found\n";
    exit;
}
$inst = json_decode(file_get_contents('vendor/composer/installed.json'), true);
$packages = $inst['packages'] ?? $inst;
foreach ($packages as $p) {
    if (in_array($p['name'], ['laravel/framework', 'filament/filament', 'filament/support', 'filament/tables', 'laravel/tinker', 'livewire/livewire'])) {
        echo $p['name'] . ": " . $p['version'] . "\n";
    }
}
