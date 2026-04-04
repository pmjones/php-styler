<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitMultiplication extends TSplitOperator
{
    public function splitPriority() : int
    {
        return ASplittable::MULTIPLICATION;
    }
}
