<?php

$dir = realpath(__DIR__ . '/..');
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));

$conflicts = [];
foreach ($rii as $file) {
    if ($file->isDir()) continue;
    $path = $file->getPathname();
    if (strpos($path, 'node_modules') !== false || strpos($path, 'vendor') !== false || strpos($path, '.git') !== false) {
        continue;
    }
    
    $content = file_get_contents($path);
    if (strpos($content, '<<<<<<<') !== false) {
        $conflicts[$path] = $content;
    }
}

echo "Total files with conflicts: " . count($conflicts) . "\n";

foreach ($conflicts as $path => $content) {
    $rel = str_replace($dir . DIRECTORY_SEPARATOR, '', $path);
    preg_match_all('/<{7}[^\r\n]*\r?\n(.*?)\r?\n={7}\r?\n(.*?)\r?\n>{7}[^\r\n]*/s', $content, $matches, PREG_SET_ORDER);
    echo "$rel: " . count($matches) . " conflict blocks\n";
}
