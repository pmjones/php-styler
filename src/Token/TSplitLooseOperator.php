<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitLooseOperator extends TSplitOperator
{
    public function splitPriority() : int
    {
        return TSplittable::LOOSE_OPERATOR;
    }
}
