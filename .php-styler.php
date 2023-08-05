<?php
use PhpStyler\Files;

return [
    'styler' => [
        'lineLen' => 80,
        'splitOrder' => [
            'concat',
            'array',
            'ternary',
            'cond',
            'bool_and',
            'precedence',
            'bool_or',
            'args-member',
            'coalesce',
            'params',
        ],
    ],
    'cache' => __DIR__ . '/.php-styler.cache',
    'files' => Files::find([
        __DIR__ . '/src',
    ])
];
