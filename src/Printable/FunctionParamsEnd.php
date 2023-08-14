<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class FunctionParamsEnd extends Printable
{
    public function __construct(public readonly int $count)
    {
    }
}
