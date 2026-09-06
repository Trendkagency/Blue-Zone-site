<?php

$content = file_get_contents('resources/views/customer/home/index.blade.php');
$lines = explode("\n", $content);

foreach ($lines as $i => $line) {
    if (preg_match('/<section/i', $line) || preg_match('/<!--\s*\d+/i', $line)) {
        echo ($i + 1) . ": " . trim($line) . "\n";
    }
}
