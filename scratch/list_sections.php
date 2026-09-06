<?php

$lines = file('resources/views/customer/home/index.blade.php');
foreach ($lines as $num => $line) {
    if (preg_match('/<section/i', $line) || preg_match('/id="[a-zA-Z0-9_\-]+"/i', $line)) {
        echo ($num + 1) . ": " . trim($line) . "\n";
    }
}
