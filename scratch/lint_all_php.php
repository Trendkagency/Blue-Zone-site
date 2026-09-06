<?php

$dirs = ['app', 'bootstrap', 'config', 'database', 'routes'];
$base = realpath(__DIR__ . '/..');

$errors = [];
$checked = 0;

foreach ($dirs as $d) {
    $full = $base . '/' . $d;
    if (!is_dir($full)) continue;
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($full, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($rii as $file) {
        if ($file->getExtension() === 'php') {
            $checked++;
            $path = $file->getPathname();
            $out = [];
            $code = 0;
            exec("php -l " . escapeshellarg($path), $out, $code);
            if ($code !== 0) {
                $errors[$path] = implode("\n", $out);
            }
        }
    }
}

echo "Checked $checked PHP files.\n";
if (empty($errors)) {
    echo "SUCCESS: 0 syntax/parse errors found in all PHP files!\n";
} else {
    echo "FAILED with " . count($errors) . " errors:\n";
    foreach ($errors as $path => $err) {
        echo "File: $path\n$err\n\n";
    }
}
