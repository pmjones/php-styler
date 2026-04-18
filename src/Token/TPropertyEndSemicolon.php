<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPropertyEndSemicolon extends AMemberClosing
{
    public function memberType() : string
    {
        return AMemberClosing::PROPERTY;
    }
}
