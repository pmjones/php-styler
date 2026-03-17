<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Token\T;

class Nesting
{
    public function __construct(
        public readonly T $token,
        public int $argCount = 0,
    ) {
    }
}
