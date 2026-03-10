<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDot extends T implements TSplittableOperator
{
    public function splitCategory() : int
    {
        return self::TIGHT_OPERATOR;
    }
}
