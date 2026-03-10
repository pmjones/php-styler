<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TWhileClosingBrace extends T implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->removeTrailingBlankLine();
        $parser->indentDecr();

        $parser->closeNesting($unparsed, self::class, TWhile::class);
    }
}
