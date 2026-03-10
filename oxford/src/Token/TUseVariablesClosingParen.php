<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TUseVariablesClosingParen extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->closeNesting($unparsed, self::class, TUseVariablesOpeningParen::class);

        $parser->popNesting(TUse::class);
    }
}
