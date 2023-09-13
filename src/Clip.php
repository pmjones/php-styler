<?php
declare(strict_types=1);

namespace PhpStyler;

class Clip
{
    public function __construct(public readonly bool $toParen = false)
    {
    }
}
