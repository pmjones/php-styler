<?php
declare(strict_types=1);

namespace PhpStyler;

use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class Files
{
    /**
     * @param string[] $dirs
     * @return string[]
     */
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

            /** @var SplFileInfo $file */
            foreach ($files as $file) {
                $found[] = $file->getPathname();
            }
        }

        return $found;
    }

    public static function filter(
        SplFileInfo $current,
        string $key,
        RecursiveDirectoryIterator $iterator,
    ) : bool
    {
        return $iterator->hasChildren()
            || str_ends_with((string) $current, '.php')
        ;
    }
}
