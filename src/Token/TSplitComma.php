<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitComma extends TSplit
{
    public function splitPriority() : int
    {
        return ASplittable::COMMA;
    }

    public function continuation() : bool
    {
        return false;
    }
}
