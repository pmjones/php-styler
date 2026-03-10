<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TIfClosingBraceless extends T implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->popNesting(TIf::class);
        $parser->indentDecr();
        $parser->noSpace();
        $parser->add($unparsed, self::class);
        $parser->space();
    }
}
