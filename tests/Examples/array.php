<?php
$foo = ['bar', 'baz', 'dib' => 'zim'];
$zim = $foo['bar'][$baz][1];
$long = [
    'veryLongElement',
    'veryLongElement',
    'veryLongElement',
    'veryLongElement',
    'veryLongElement',
    'veryLongElement',
    'veryLongElement',
    'veryLongElement',
    'veryLongElement',
    'veryLongElement',
];
$longWithComments = [
    // one
    'veryLongElement',

    // two
    'veryLongElement',
    'veryLongElement',

    // three
    'veryLongElement',
    'veryLongElement',
    'veryLongElement',

    // four
    'veryLongElement',
    'veryLongElement',
    'veryLongElement',
    'veryLongElement',
];
$veryVeryVeryVeryVeryVeryLongVariableName = [
    34 => 'quot',
    38 => 'amp',
    60 => 'lt',
    62 => 'gt',
];

if (true) {
    if (true) {
        if (true) {
            $buckets[$quality][] = [
                'value' => trim($value),
                'quality' => $quality,
                'params' => $params,
            ];
        }
    }
}
