<?php
declare(strict_types=1);

namespace PhpStyler\Printable;

class ParamName extends Printable
{
    public function __construct(
        public readonly string $str
    ) {
    }
}
