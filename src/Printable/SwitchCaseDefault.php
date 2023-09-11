<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class SwitchCaseDefault extends Printable
{
    public function __construct(public readonly bool $hasBody)
    {
    }
}
