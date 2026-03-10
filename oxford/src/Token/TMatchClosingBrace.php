<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TMatchClosingBrace extends T implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->removeTrailingBlankLine();
        $parser->lineBreak();
        $parser->indentDecr();

        $parser->closeNesting($unparsed, self::class, TMatch::class);
    }
}
