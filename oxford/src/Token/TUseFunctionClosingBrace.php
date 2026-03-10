<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TUseFunctionClosingBrace extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->noSpace();
        $parser->add($unparsed, static::class);
        $parser->space();
    }
}
