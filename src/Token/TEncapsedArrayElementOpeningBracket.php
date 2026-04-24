<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TEncapsedArrayElementOpeningBracket extends AToken
{
    public function isOpener() : bool
    {
        return false;
    }
}
