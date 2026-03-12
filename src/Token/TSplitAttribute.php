<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitAttribute extends TSplit
{
    public function splitPriority() : int
    {
        return TSplittable::ATTRIBUTE;
    }
}
