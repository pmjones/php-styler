<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitComma extends TSplitPoint
{
    public function splitPriority() : int
    {
        return TSplittable::COMMA;
    }

    public function continuation() : bool
    {
        return false;
    }
}
