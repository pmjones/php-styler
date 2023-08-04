<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class Modifiers extends Printable
{
    public function __construct(public readonly int $flags)
    {
    }
}
