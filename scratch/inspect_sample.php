<?php

function inspectFile($rel) {
    $path = __DIR__ . '/../' . $rel;
    if (!file_exists($path)) return;
    $c = file_get_contents($path);
    preg_match_all('/<{7}[^\r\n]*\r?\n(.*?)\r?\n={7}\r?\n(.*?)\r?\n>{7}[^\r\n]*/s', $c, $m, PREG_SET_ORDER);
    echo "=== $rel (" . count($m) . " blocks) ===\n";
    foreach ($m as $i => $match) {
        echo "--- Block $i ---\n";
        echo "[HEAD]:\n" . substr($match[1], 0, 300) . "\n";
        echo "[ORIGIN]:\n" . substr($match[2], 0, 300) . "\n\n";
    }
}

inspectFile('app/Http/Controllers/Admin/CategoryController.php');
inspectFile('app/Models/Product.php');
inspectFile('routes/admin.php');
inspectFile('app/Providers/AppServiceProvider.php');
