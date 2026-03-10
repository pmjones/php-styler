<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TAnonymousClosingBrace extends T implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->removeTrailingBlankLine();
        $parser->indentDecr();

        $parser->closeNesting($unparsed, self::class, TAnonymousClass::class, TAnonymousFunction::class);
    }
}
