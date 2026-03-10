<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TDynamicVariableClosingBrace extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->closeNesting($unparsed, self::class, TDynamicVariableOpeningBrace::class);
    }
}
