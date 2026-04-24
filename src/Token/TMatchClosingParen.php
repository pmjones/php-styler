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

        // PHP normally requires `{` after `match (...)`; the braceless
        // path handles malformed input so it fails loudly later rather
        // than producing silently-broken output.
        if (! $parser->source->peek()?->is(['{', ':'])) {
            $parser->parse($source, TOpeningBraceless::class);
        }
    }
}
