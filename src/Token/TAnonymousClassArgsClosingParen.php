<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TAnonymousClassArgsClosingParen extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->closeNesting(
            $source,
            self::class,
            TAnonymousClassArgsOpeningParen::class,
        );
    }
}
