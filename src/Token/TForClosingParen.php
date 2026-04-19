<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TForClosingParen extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->getPrevParsed() instanceof TLoopEmptySemicolon) {
            $parser->parse($source, TLoopEmptyClosingParen::class);
            return;
        }

        $parser->closeNesting($source, self::class, TForOpeningParen::class);

        if (! $parser->source->peek()?->is(['{', ':', ';'])) {
            $parser->parse($source, TOpeningBraceless::class);
        }
    }
}
