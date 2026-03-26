<?php
use PhpStyler\Config;
use PhpStyler\Files;
use PhpStyler\Format\DeclarationFormat;

return new Config(
    files: new Files(__DIR__ . '/src'),
    cache: __DIR__ . '/.php-styler.cache',
    format: new DeclarationFormat(
        lineLen: 84,
        indentLen: 4,
        indentTab: false,
        eol: "\n",
    ),
);
