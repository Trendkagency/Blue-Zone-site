<?php
$lock = json_decode(file_get_contents('composer.lock'), true);
echo "=== PACKAGES (" . count($lock['packages']) . ") ===\n";
foreach ($lock['packages'] as $p) {
    echo $p['name'] . " (" . $p['version'] . ")\n";
}
echo "=== DEV PACKAGES (" . count($lock['packages-dev'] ?? []) . ") ===\n";
foreach ($lock['packages-dev'] ?? [] as $p) {
    echo $p['name'] . " (" . $p['version'] . ")\n";
}
