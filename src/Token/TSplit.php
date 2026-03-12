<?php
declare(strict_types=1);

namespace PhpStyler\Token;

abstract class TSplit extends T
{
    abstract public function splitPriority() : int;

    public function continuation() : bool
    {
        return true;
    }

    public function shouldSkipFirst(int $totalPositions) : bool
    {
        return false;
    }

    public function markAsMethodCall() : void
    {
    }
}
