<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TSwitchClosingParen extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->closeNesting($source, self::class, TSwitchOpeningParen::class);

        // @codeCoverageIgnoreStart
        // defensive: PHP syntactically requires `{` (or `:` for alt syntax)
        // after `switch (...)`; the braceless path is unreachable for
        // accepted input
        if (! $parser->source->peek()?->is(['{', ':'])) {
            $parser->parse($source, TOpeningBraceless::class);
        }
        // @codeCoverageIgnoreEnd
    }
}
