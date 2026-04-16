<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TForClosingBrace extends AToken implements AClosingStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->removeTrailingBlankLine();
        $parser->indentDecr();

        $parser->closeNesting($source, self::class, TFor::class);

        if ($parser->atNesting(TOpeningBraceless::class)) {
            $parser->endBracelessBody($source);
        }
    }
}
