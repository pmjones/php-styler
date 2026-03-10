<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TForeachClosingBraceless extends T implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->popNesting(TForeach::class);
        $parser->indentDecr();
        $parser->noSpace();
        $parser->add($unparsed, self::class);
        $parser->space();
    }
}
