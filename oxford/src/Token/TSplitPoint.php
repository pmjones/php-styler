<?php
declare(strict_types=1);

namespace Oxford\Token;

class TSplitPoint extends T
{
    public int $splitPriority = 0;

    public bool $continuation = true;

    public bool $isMethodCall = false;

    public function shouldSkipFirst(int $totalPositions) : bool
    {
        return $this->splitPriority === TSplittable::FLUENT
            && ($totalPositions === 1 || ! $this->isMethodCall);
    }

    public function markAsMethodCall() : void
    {
        $this->isMethodCall = true;
    }
}
