<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitBitwiseOr extends TSplitOperator
{
    public function splitPriority() : int
    {
        return ASplittable::BITWISE_OR;
    }
}
