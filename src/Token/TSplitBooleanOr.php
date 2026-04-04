<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitBooleanOr extends TSplitOperator
{
    public function splitPriority() : int
    {
        return ASplittable::BOOLEAN_OR;
    }
}
