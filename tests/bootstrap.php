<?php

declare(strict_types=1);

/**
 * Bootstrap untuk PHPUnit tests.
 */

// Muat autoloader Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Definisikan konstanta HOMEPATH yang dibutuhkan oleh bootstrap framework CI4
if (! defined('HOMEPATH')) {
    define('HOMEPATH', realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR);
}

// Muat bootstrap framework CI4 secara penuh untuk testing
require_once __DIR__ . '/../vendor/codeigniter4/framework/system/Test/bootstrap.php';

// Run migrations once globally
$migrations = \Config\Services::migrations();
try {
    $migrations->latest();
    echo "Global migrations completed successfully.\n";
} catch (\Throwable $e) {
    echo "Global migrations failed: " . $e->getMessage() . "\n";
    exit(1);
}
