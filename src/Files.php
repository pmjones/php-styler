<?php
declare(strict_types=1);

namespace PhpStyler;

use Generator;
use IteratorAggregate;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Traversable;

/**
 * @implements IteratorAggregate<int, string>
 */
class Files implements IteratorAggregate
{
    /**
     * @var string[]
     */
    protected array $dirs = [];

    public function __construct(string ...$dirs)
    {
        $this->dirs = $dirs;
    }

    public function getIterator() : Generator
    {
        foreach ($this->dirs as $dir) {
            $files = new RecursiveIteratorIterator(
                new RecursiveCallbackFilterIterator(
                    new RecursiveDirectoryIterator($dir),
                    fn ($c, $k, $i) => self::filter($c, $k, $i),
                ),
            );

            /** @var SplFileInfo $file */
            foreach ($files as $file) {
                yield $file->getPathname();
            }
        }
    }

    public function filter(
        SplFileInfo $current,
        string $key,
        RecursiveDirectoryIterator $iterator,
    ) : bool
    {
        return $iterator->hasChildren() || str_ends_with((string) $current, '.php');
    }
}
