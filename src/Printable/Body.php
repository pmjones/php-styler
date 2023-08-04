<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class Body extends Printable
{
    public function __construct(public readonly string $type)
    {
    }
}
