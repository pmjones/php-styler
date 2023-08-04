<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class Closure extends Printable
{
    public function __construct(public readonly bool $static)
    {
    }
}
