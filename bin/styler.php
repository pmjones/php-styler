<?php
use AutoShell\Console;

require dirname(__DIR__) . '/vendor/autoload.php';

$console = Console::new(
    namespace: 'PhpStyler\Command',
    directory: dirname(__DIR__) . '/src/Command',
    help: 'PHPStyler by Paul M. Jones' . PHP_EOL . PHP_EOL,
);

$code = $console($_SERVER['argv']);
exit($code);
