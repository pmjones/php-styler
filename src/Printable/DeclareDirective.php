<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class DeclareDirective extends Printable
{
    public function __construct(public readonly string $name)
    {
    }
}
