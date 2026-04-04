<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitFnDoubleArrow extends TSplitOperator
{
    public function splitPriority() : int
    {
        return ASplittable::FN_ARROW;
    }
}
