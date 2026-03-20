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

        if (! $parser->getNextSource()?->is(['{', ':'])) {
            $parser->parse($source, TOpeningBraceless::class);
        }
    }
}
