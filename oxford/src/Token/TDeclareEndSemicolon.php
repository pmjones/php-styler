<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TDeclareEndSemicolon extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->popNesting(TDeclare::class);

        $parser->add($unparsed, self::class);
    }
}
