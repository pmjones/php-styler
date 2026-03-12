<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDot extends T implements TSplittableOperator
{
    public function splitPointBefore() : ?TSplitPoint
    {
        return TSplitOperator::new (self::TIGHT_OPERATOR);
    }
}
