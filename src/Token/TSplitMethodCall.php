<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitMethodCall extends TSplitFluent
{
    public function shouldSkipFirst(int $totalPositions) : bool
    {
        return $totalPositions === 1;
    }
}
