<?php
declare(strict_types=1);

namespace PhpStyler\Token;

abstract class TSplit extends AToken
{
    public function isContent() : bool
    {
        return false;
    }

    abstract public function splitPriority() : int;

    public function continuation() : bool
    {
        return true;
    }

    public function shouldSkipFirst() : bool
    {
        return false;
    }
}
