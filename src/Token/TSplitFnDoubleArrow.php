<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitFnDoubleArrow extends TSplitOperator
{
    public function splitPriority() : int
    {
        return TSplittable::FN_DOUBLE_ARROW;
    }
}
