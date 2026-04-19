<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TForeachClosingParen extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->closeNesting($source, self::class, TForeachOpeningParen::class);

        if (! $parser->source->peek()?->is(['{', ':', ';'])) {
            $parser->parse($source, TOpeningBraceless::class);
        }
    }
}
