<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitAddition extends TSplitOperator
{
    public function splitPriority() : int
    {
        return TSplittable::ADDITION;
    }
}
