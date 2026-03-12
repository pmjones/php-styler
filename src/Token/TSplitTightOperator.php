<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitTightOperator extends TSplitOperator
{
    public function splitPriority() : int
    {
        return TSplittable::TIGHT_OPERATOR;
    }
}
