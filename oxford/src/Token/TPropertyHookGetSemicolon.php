<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TPropertyHookGetSemicolon extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if ($parser->atNesting(TPropertyHookGetDoubleArrow::class)) {
            $parser->popNesting(TPropertyHookGetDoubleArrow::class);
        }

        $parser->popNesting(TPropertyHookGet::class);

        $parser->add($unparsed, self::class);
    }
}
