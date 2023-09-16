<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class Body extends Printable
{
    /**
     * @param mixed[] $info
     */
    public function __construct(
        public readonly string $type,
        public readonly array $info = [],
    ) {
    }
}
