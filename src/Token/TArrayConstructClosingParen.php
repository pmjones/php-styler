<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TArrayConstructClosingParen extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->closeNesting(
            $source,
            self::class,
            TArrayConstructOpeningParen::class,
        );

        $parser->popNesting(TArrayConstruct::class);
    }
}
