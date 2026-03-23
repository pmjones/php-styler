<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TClosingParen extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popTernaryNesting();

        /** @var class-string<AToken> $closingParenClass */
        $closingParenClass = str_replace(
            'Opening',
            'Closing',
            $parser->getNesting(),
        );

        if ($closingParenClass !== self::class) {
            $parser->parse($source, $closingParenClass);
            return;
        }

        $parser->closeNesting($source, self::class, TOpeningParen::class);
    }
}
