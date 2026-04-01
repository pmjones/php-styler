<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitPropertyAccess extends TSplitFluent
{
    public function shouldSkipFirst() : bool
    {
        return true;
    }
}
