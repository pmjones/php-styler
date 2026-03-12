<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitStaticMember extends TSplitFluent
{
    public function shouldSkipFirst(int $totalPositions) : bool
    {
        return true;
    }
}
