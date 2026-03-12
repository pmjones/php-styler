<?php
declare(strict_types=1);

namespace PhpStyler\Token;

abstract class TSplitFluent extends TSplit
{
    public function splitPriority() : int
    {
        return TSplittable::FLUENT;
    }
}
