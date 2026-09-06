<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$slugs = [
    'blue-mind',
    'blue-cell',
    'blue-defense',
    'blue-metabolic',
    'blue-sleep',
    'blue-vitality',
];

$allOk = true;

foreach ($slugs as $slug) {
    // 1. Science Details Route
    $req = Illuminate\Http\Request::create("/our-science/{$slug}", 'GET');
    $res = $kernel->handle($req);
    $sCode = $res->getStatusCode();
    if ($sCode === 200) {
        echo " [OK 200] /our-science/{$slug}\n";
    } else {
        $allOk = false;
        echo " [FAIL {$sCode}] /our-science/{$slug}\n";
    }

    // 2. Product Show Route
    $req2 = Illuminate\Http\Request::create("/products/{$slug}", 'GET');
    $res2 = $kernel->handle($req2);
    $pCode = $res2->getStatusCode();
    if ($pCode === 200) {
        echo " [OK 200] /products/{$slug}\n";
    } else {
        $allOk = false;
        echo " [FAIL {$pCode}] /products/{$slug}\n";
    }
}

if ($allOk) {
    echo "\n>>> ALL 6 PRODUCTS & SCIENCE DOSSIERS RETURN 200 OK! <<<\n";
}
