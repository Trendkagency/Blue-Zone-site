<?php

$dir = __DIR__ . '/..';
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));

$conflictFiles = [];
foreach ($rii as $file) {
    if ($file->isDir()) continue;
    $path = $file->getPathname();
    if (strpos($path, 'node_modules') !== false || strpos($path, 'vendor') !== false || strpos($path, '.git') !== false) {
        continue;
    }
    
    $content = file_get_contents($path);
    if (strpos($content, '<<<<<<<') !== false) {
        $conflictFiles[] = $path;
    }
}

echo "Found " . count($conflictFiles) . " files with conflict markers.\n";
foreach ($conflictFiles as $f) {
    echo " - " . str_replace($dir, '', $f) . "\n";
}
