<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class UseTraitInsteadof extends Printable
{
    public function __construct(
        public readonly string $trait,
        public readonly string $method,
    ) {
    }
}
