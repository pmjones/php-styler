<?php
use PhpStyler\Config;
use PhpStyler\Files;
use PhpStyler\Token;

return new Config(
    eol: "\n",
    lineLen: 88,
    indentLen: 4,
    indentTab: false,
    cache: __DIR__ . '/.php-styler.cache',
    files: new Files(
        __DIR__ . '/src',
        __DIR__ . '/tests/AssemblerTest.php',
        __DIR__ . '/tests/ConfigTest.php',
        __DIR__ . '/tests/ExamplesTest.php',
        __DIR__ . '/tests/FilesTest.php',
        __DIR__ . '/tests/SplitterTest.php',
        __DIR__ . '/tests/StylerTest.php',
        __DIR__ . '/tests/TestCase.php',
    ),
);
