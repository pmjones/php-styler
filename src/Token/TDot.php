<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDot extends T implements TSplittableOperator
{
    public function splitBefore() : ?TSplit
    {
        return TSplitOperator::new (self::TIGHT_OPERATOR);
    }
}
