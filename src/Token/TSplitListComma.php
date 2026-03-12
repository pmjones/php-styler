<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TSplitListComma extends TSplitComma
{
    public function continuation() : bool
    {
        return true;
    }
}
