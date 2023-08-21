<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class PrecedenceEnd extends Printable
{
    public function __construct(public readonly bool $ternary)
    {
    }
}
