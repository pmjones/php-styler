<?php
declare(strict_types=1);

namespace PhpStyler;

use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Files
{
    public static function find(array $dirs) : array
    {
        $found = [];

        foreach ($dirs as $dir) {
            $files = new RecursiveIteratorIterator(
                new RecursiveCallbackFilterIterator(
                    new RecursiveDirectoryIterator($dir),
                    fn ($c, $k, $i) => self::filter($c, $k, $i),
                ),
            );

            foreach ($files as $file) {
                $found[] = (string) $file;
            }
        }

        return $found;
    }

    public static function filter($current, $key, $iterator) : bool
    {
        return $iterator->hasChildren()
            || str_ends_with((string) $current, '.php')
        ;
    }
}
