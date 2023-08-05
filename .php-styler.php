<?php
use PhpStyler\Files;

return [
    'styler' => [
        'lineLen' => 80,
        'splitOrder' => [
            'concat',
            'array_1',
            'array_2',
            'array_3',
            'array_4',
            'array_5',
            'ternary',
            'cond',
            'bool_and',
            'precedence',
            'bool_or',
            'args_1',
            'member_1',
            'args_2',
            'member_2',
            'args_3',
            'member_3',
            'args_4',
            'member_4',
            'args_5',
            'member_5',
            'coalesce',
            'params',
        ],
    ],
    'cache' => __DIR__ . '/.php-styler.cache',
    'files' => Files::find([
        __DIR__ . '/src',
    ])
];
