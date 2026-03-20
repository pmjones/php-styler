<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TMatchClosingBrace extends AToken implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->removeTrailingBlankLine();
        $parser->lineBreak();
        $parser->indentDecr();

        $parser->closeNesting($source, self::class, TMatch::class);
    }
}
