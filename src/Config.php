<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Format\AFormat;
use PhpStyler\Format\PlainFormat;

class Config
{
    /**
     * @param string[] $files
     */
    public function __construct(
        public readonly iterable $files,
        public readonly AFormat $format = new PlainFormat(),
    ) {
    }
}
