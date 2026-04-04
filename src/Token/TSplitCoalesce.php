<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitCoalesce extends TSplitOperator
{
    public function splitPriority() : int
    {
        return ASplittable::COALESCE;
    }
}
