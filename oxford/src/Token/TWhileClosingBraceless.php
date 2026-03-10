<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TWhileClosingBraceless extends T implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->popNesting(TWhile::class);
        $parser->indentDecr();
        $parser->noSpace();
        $parser->add($unparsed, self::class);
        $parser->space();
    }
}
