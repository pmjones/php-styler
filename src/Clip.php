<?php
declare(strict_types=1);

namespace PhpStyler;

class Clip
{
    /**
     * @param callable $condition
     */
    public function __construct(
        public readonly mixed $condition = null,
        public readonly string $append = '',
    ) {
    }
}
