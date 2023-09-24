<?php
use PhpStyler\Config;
use PhpStyler\Files;
use PhpStyler\Styler;

return new Config(
    cache: __DIR__ . '/.php-styler.cache',
    files: new Files(
        __DIR__ . '/src',
        __DIR__ . '/tests/ConfigTest.php',
        __DIR__ . '/tests/ExamplesTest.php',
        __DIR__ . '/tests/FilesTest.php',
        __DIR__ . '/tests/IrkStyler.php',
        __DIR__ . '/tests/IrkStylerTest.php',
        __DIR__ . '/tests/IssuesTest.php',
        __DIR__ . '/tests/LineTest.php',
        __DIR__ . '/tests/NestingTest.php',
        __DIR__ . '/tests/TestCase.php',
    ),
    styler: new Styler(
        eol: "\n",
    ),
);
