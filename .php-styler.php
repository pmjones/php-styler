<?php
use PhpStyler\Files;

return [
    'styler' => [
        'lineLen' => 80,
        'split' => [
            'concat',
            'array',
            'ternary',
            'cond',
            'bool_and',
            'precedence',
            'bool_or',
            'args_member',
            'coalesce',
            'params',
            'attribute_args',
        ],
    ],
    'cache' => __DIR__ . '/.php-styler.cache',
    'files' => Files::find([
        __DIR__ . '/src',
    ])
];
