<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TEnumClosingBrace extends TClasslikeClosingBrace
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->removeTrailingBlankLine();
        $parser->indentDecr();
        $parser->noSpace();
        $parser->closeNesting($source, self::class, TEnum::class);
        $parser->space();
    }
}
