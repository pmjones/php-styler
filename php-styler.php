<?php
use PhpStyler\Config;
use PhpStyler\Files;
use PhpStyler\Styler;

return new Config(
    files: new Files(
        __DIR__ . '/src',
        __DIR__ . '/tests/ExtStyler.php',
        __DIR__ . '/tests/ExtStylerTest.php',
        __DIR__ . '/tests/ConfigTest.php',
        __DIR__ . '/tests/ExamplesTest.php',
        __DIR__ . '/tests/FilesTest.php',
        __DIR__ . '/tests/LineTest.php',
        __DIR__ . '/tests/NestingTest.php',
        __DIR__ . '/tests/StylerTest.php',
        __DIR__ . '/tests/TestCase.php',
    ),
    styler: new Styler(),
);
