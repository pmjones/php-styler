<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitMatchDoubleArrow extends TSplitOperator
{
    public function splitPriority() : int
    {
        return ASplittable::MATCH_ARROW;
    }
}
