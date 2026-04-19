<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TWhileClosingParen extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->closeNesting($source, self::class, TWhileOpeningParen::class);

        if (
            $parser->atNesting(TWhile::class) && $parser->source->peek()?->is(';')
        ) {
            // do-while: leave TWhile nesting for TDoWhileEndSemicolon to handle
            return;
        }

        if (! $parser->source->peek()?->is(['{', ':', ';'])) {
            $parser->parse($source, TOpeningBraceless::class);
        }
    }
}
