<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitTernary extends TSplitOperator
{
    public function splitPriority() : int
    {
        return ASplittable::TERNARY;
    }
}
