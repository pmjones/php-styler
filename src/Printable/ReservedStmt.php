<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class ReservedStmt extends Printable
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}
