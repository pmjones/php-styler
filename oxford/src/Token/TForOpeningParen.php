<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TForOpeningParen extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
    }
}
