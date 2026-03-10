<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPropertyHookSetSemicolon extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if ($parser->atNesting(TPropertyHookSetDoubleArrow::class)) {
            $parser->popNesting(TPropertyHookSetDoubleArrow::class);
        }

        $parser->popNesting(TPropertyHookSet::class);

        $parser->add($unparsed, self::class);
    }
}
