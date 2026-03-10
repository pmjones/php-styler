<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TElseClosingBraceless extends T implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->popNesting(TElse::class);
        $parser->indentDecr();
        $parser->noSpace();
        $parser->add($unparsed, self::class);
        $parser->space();
    }
}
