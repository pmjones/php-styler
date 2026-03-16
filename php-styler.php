<?php
use PhpStyler\Config;
use PhpStyler\Files;
use PhpStyler\Rule\AddPublicVisibility;
use PhpStyler\Rule\ConvertElseIf;
use PhpStyler\Rule\ConvertListToArray;
use PhpStyler\Rule\ConvertLongArrayToShort;
use PhpStyler\Rule\ExpandImports;
use PhpStyler\Rule\NormalizeTrailingCommas;
use PhpStyler\Rule\OrderImports;
use PhpStyler\Rule\OrderModifiers;
use PhpStyler\Token;

return new Config(
    eol: "\n",
    lineLen: 88,
    indentLen: 4,
    indentTab: false,
    rules: [
        new ConvertListToArray(),
        new ConvertLongArrayToShort(),
        new ConvertElseIf(),
        new ExpandImports(),
        new OrderImports(),
        new AddPublicVisibility(),
        new OrderModifiers(),
        new NormalizeTrailingCommas(),
    ],
    cache: __DIR__ . '/.php-styler.cache',
    files: new Files(
        __DIR__ . '/src',
        __DIR__ . '/tests/Style',
        __DIR__ . '/tests/Token',
        __DIR__ . '/tests/AssemblerTest.php',
        __DIR__ . '/tests/ConfigTest.php',
        __DIR__ . '/tests/ExamplesTest.php',
        __DIR__ . '/tests/FilesTest.php',
        __DIR__ . '/tests/SplitterTest.php',
        __DIR__ . '/tests/StylerTest.php',
        __DIR__ . '/tests/TestCase.php',
    ),
);
