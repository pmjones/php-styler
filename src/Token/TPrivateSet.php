<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPrivateSet extends AToken implements AModifier
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->handleModifier();
    }
}
