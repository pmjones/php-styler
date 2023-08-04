<?php
return [
    'files' => new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator(__DIR__ . '/src'),
            function ($current, $key, $iterator) {
                return $iterator->hasChildren()
                    || str_ends_with((string) $current, '.php');
            },
        )
    )
];