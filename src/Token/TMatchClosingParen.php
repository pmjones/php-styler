<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TMatchClosingParen extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->closeNesting($source, self::class, TMatchOpeningParen::class);

        // @codeCoverageIgnoreStart
        // defensive: PHP syntactically requires `{` after `match (...)`;
        // the braceless path is unreachable for accepted input
        if (! $parser->source->peek()?->is(['{', ':'])) {
            $parser->parse($source, TOpeningBraceless::class);
        }
        // @codeCoverageIgnoreEnd
    }
}
