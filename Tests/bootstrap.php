<?php

// If tests are un from within bundle.
$file = __DIR__ . '/../vendor/autoload.php';

if (!file_exists($file)) {
    // If ran from within a symfony project
    $file = __DIR__ . '/../../../../vendor/autoload.php';
    if (!file_exists($file)) {
        throw new RuntimeException('Install dependencies to run test suite.');
    }
}

$autoload = require_once $file;