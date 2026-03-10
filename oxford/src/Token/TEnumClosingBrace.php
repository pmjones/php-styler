<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TEnumClosingBrace extends TClasslikeClosingBrace
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->removeTrailingBlankLine();
        $parser->indentDecr();
        $parser->noSpace();
        $parser->closeNesting($unparsed, self::class, TEnum::class);
        $parser->space();
    }
}
