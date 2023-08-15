<?php
use PhpStyler\Config;
use PhpStyler\Files;
use PhpStyler\Styler;

return new Config(
    files: new Files(
        __DIR__ . '/src',
        __DIR__ . '/tests/ExamplesTest.php',
        __DIR__ . '/tests/StylerTest.php',
        __DIR__ . '/tests/TestCase.php',
    ),
    styler: new Styler(),
);
