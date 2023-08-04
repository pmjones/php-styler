<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class MemberEnd extends Printable
{
    public function __construct(public readonly string $operator)
    {
    }
}
