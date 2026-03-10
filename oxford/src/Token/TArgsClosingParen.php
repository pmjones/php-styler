<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TArgsClosingParen extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->closeNesting($unparsed, self::class, TArgsOpeningParen::class);
    }
}
