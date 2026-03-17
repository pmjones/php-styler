<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\DefaultFormat;
use PhpStyler\Format\Format;

class Config
{
    public function __construct(
        public readonly iterable $files,
        public readonly ?string $cache,
        public readonly Format $format = new DefaultFormat(),
    ) {
    }
}
