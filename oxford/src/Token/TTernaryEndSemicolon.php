<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TTernaryEndSemicolon extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->popNesting(TTernaryColon::class);

        $parser->add($unparsed, self::class);
    }
}
