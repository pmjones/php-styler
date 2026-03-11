<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TElseClosingBraceless extends T implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popNesting(TElse::class);
        $parser->indentDecr();
        $parser->noSpace();
        $parser->add($source, self::class);
        $parser->space();
    }
}
