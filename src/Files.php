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
    protected array $paths = [];

    public function __construct(string ...$paths)
    {
        $this->paths = $paths;
    }

    public function getIterator() : Generator
    {
        foreach ($this->paths as $path) {
            if (is_file($path) && str_ends_with($path, '.php')) {
                yield $path;
                continue;
            }

            $files = new RecursiveIteratorIterator(
                new RecursiveCallbackFilterIterator(
                    new RecursiveDirectoryIterator($path),
                    fn ($c, $k, $i) => $this->filter($c, $k, $i),
                ),
            );

            /** @var SplFileInfo $file */
            foreach ($files as $file) {
                yield $file->getPathname();
            }
        }
    }

    protected function filter(
        SplFileInfo $current,
        string $key,
        RecursiveDirectoryIterator $iterator,
    ) : bool
    {
        return $iterator->hasChildren() || str_ends_with((string) $current, '.php');
    }
}
