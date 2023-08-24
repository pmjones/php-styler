<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class SwitchCaseEnd extends Printable
{
    public function __construct(public readonly bool $hasBody)
    {
    }
}
