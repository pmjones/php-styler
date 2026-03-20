<?php
declare(strict_types=1);

namespace PhpStyler;

use PhpStyler\Token\AToken;

class Nesting
{
    public function __construct(public readonly AToken $token, public int $argCount = 0)
    {
    }
}
