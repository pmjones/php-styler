<?php
use PhpStyler\Config;
use PhpStyler\Files;

return new Config(
    files: new Files(__DIR__ . '/src'),
    cache: __DIR__ . '/.php-styler.cache',
);
