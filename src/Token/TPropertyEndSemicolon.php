<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TPropertyEndSemicolon extends AToken implements AMemberClosing
{
    public bool $closesStaticMember = false;

    public function memberType() : string
    {
        return AMemberClosing::PROPERTY;
    }
}
