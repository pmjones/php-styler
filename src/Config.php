<?php
declare(strict_types=1);

namespace PhpStyler;

class Config
{
    /**
     * @param string[] $files
     */
    public function __construct(
        public readonly iterable $files,
        public readonly ?string $cache,
        public string $eol = "\n",
        public int $lineLen = 88,
        public int $indentLen = 4,
        public bool $indentTab = false,
    ) {
    }
}
