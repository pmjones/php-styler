<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

// HTML coverage with xdebug peaks well above PHP's 128M default; 512M gives
// headroom on the current suite and leaves room to grow
ini_set('memory_limit', '512M');

$autoloader = __DIR__ . '/vendor/autoload.php';
if (! file_exists($autoloader)) {
    echo "Composer autoloader not found: $autoloader" . PHP_EOL;
    echo "Please issue 'composer install' and try again." . PHP_EOL;
    exit(1);
}
require $autoloader;
