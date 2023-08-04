<?php
use PhpStyler\Files;

return [
    'cache' => __DIR__ . '/.php-styler.cache',
    'files' => Files::find([
        __DIR__ . '/src',
    ])
];
