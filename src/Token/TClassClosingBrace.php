<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TClassClosingBrace extends TClasslikeClosingBrace
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->removeTrailingBlankLine();
        $parser->indentDecr();
        $parser->closeNesting($source, self::class, TClass::class);
    }
}
