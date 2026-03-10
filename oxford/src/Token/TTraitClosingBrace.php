<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TTraitClosingBrace extends TClasslikeClosingBrace
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->removeTrailingBlankLine();
        $parser->indentDecr();
        $parser->noSpace();
        $parser->closeNesting($unparsed, self::class, TTrait::class);
        $parser->space();
    }
}
