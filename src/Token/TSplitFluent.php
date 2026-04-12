<?php
declare(strict_types=1);

namespace PhpStyler\Token;

abstract class TSplitFluent extends TSplit
{
    public int $chainIndex = 0;

    public int $chainPosition = 0;

    public function splitPriority() : int
    {
        return ASplittable::FLUENT;
    }
}
