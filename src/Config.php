<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\Format;
use PhpStyler\Format\PlainFormat;

class Config
{
    /**
     * @param string[] $files
     */
    public function __construct(
        public readonly iterable $files,
        public readonly ?string $cache,
        public readonly Format $format = new PlainFormat(),
    ) {
    }
}
