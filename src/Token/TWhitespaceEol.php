<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TWhitespaceEol extends AToken implements ALineBreaking
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if (
            $parser->hasPrev(TWhitespaceEol::class)
            || $parser->hasPrev(TBlankLine::class)
        ) {
            $parser->blankLine();
            return;
        }

        $parser->add($source, static::class);
    }
}
