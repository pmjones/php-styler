<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitForSemicolon extends TSplitPoint
{
    public function splitPriority() : int
    {
        return TSplittable::FOR_SEMICOLON;
    }

    public function continuation() : bool
    {
        return false;
    }
}
