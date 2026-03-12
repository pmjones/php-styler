<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitFluent extends TSplitPoint
{
    private bool $isMethodCall = false;

    public function splitPriority() : int
    {
        return TSplittable::FLUENT;
    }

    public function shouldSkipFirst(int $totalPositions) : bool
    {
        return $totalPositions === 1 || ! $this->isMethodCall;
    }

    public function markAsMethodCall() : void
    {
        $this->isMethodCall = true;
    }
}
