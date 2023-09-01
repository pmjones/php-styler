<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class ArgsEnd extends Printable
{
    public function __construct(
        public readonly int $count,
        public readonly ?bool $expansive,
    ) {
    }
}
