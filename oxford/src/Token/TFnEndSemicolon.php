<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TFnEndSemicolon extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->popNesting(TFnDoubleArrow::class);
        $parser->popNesting(TFn::class);

        $parser->add($unparsed, self::class);
    }
}
