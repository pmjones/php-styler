<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class Separator extends Printable
{
    public function __construct(
        public readonly string $type,
        public readonly Printable $orig,
    ) {
    }
}
