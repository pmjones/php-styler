<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitBitwiseAnd extends TSplitOperator
{
    public function splitPriority() : int
    {
        return ASplittable::BITWISE_AND;
    }
}
