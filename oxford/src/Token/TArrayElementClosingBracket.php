<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TArrayElementClosingBracket extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->noSpace();
        $parser->closeNesting($unparsed, self::class, TArrayElementOpeningBracket::class);
        $parser->space();
    }
}
