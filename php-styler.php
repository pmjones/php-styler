<?php
use PhpStyler\Config;
use PhpStyler\Files;
use PhpStyler\Format;

return new Config(
    cache: __DIR__ . '/.php-styler.cache',
    files: new Files(
        __DIR__ . '/src',
        __DIR__ . '/tests/Rule',
        __DIR__ . '/tests/Token',
        __DIR__ . '/tests/AssemblerTest.php',
        __DIR__ . '/tests/ConfigTest.php',
        __DIR__ . '/tests/DocblockTagTest.php',
        __DIR__ . '/tests/ExamplesTest.php',
        __DIR__ . '/tests/FilesTest.php',
        __DIR__ . '/tests/FormatStyleTest.php',
        __DIR__ . '/tests/SplitterTest.php',
        __DIR__ . '/tests/StylerTest.php',
        __DIR__ . '/tests/StyleTest.php',
        __DIR__ . '/tests/TestCase.php',
    ),
    format: new Format\DeclarationFormat(),
);
