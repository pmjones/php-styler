<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitForComma extends TSplit
{
    public function splitPriority() : int
    {
        return ASplittable::FOR_COMMA;
    }

    public function continuation() : bool
    {
        return false;
    }
}
