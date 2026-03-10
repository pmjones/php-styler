<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TElseifContinuationBraceless extends T implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->popNesting(TElseif::class);
        $parser->indentDecr();
        $parser->add($unparsed, self::class);
        $parser->space();
    }
}
