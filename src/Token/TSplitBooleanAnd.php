<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitBooleanAnd extends TSplitOperator
{
    public function splitPriority() : int
    {
        return TSplittable::BOOLEAN_AND;
    }
}
