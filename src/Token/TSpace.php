<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSpace extends AToken
{
    public function isContent() : bool
    {
        return false;
    }
}
