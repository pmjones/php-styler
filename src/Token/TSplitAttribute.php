<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitAttribute extends TSplitPoint
{
    public function splitPriority() : int
    {
        return TSplittable::ATTRIBUTE;
    }
}
