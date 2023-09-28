<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class InlineComment extends Printable
{
    public function __construct(
        public readonly string $text,
        public readonly bool $trailing,
    ) {
    }
}
