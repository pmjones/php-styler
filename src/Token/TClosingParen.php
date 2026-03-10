<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TClosingParen extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->popTernaryNesting();
        /** @var class-string<T> $closingParenClass */
        $closingParenClass = str_replace('Opening', 'Closing', $parser->getNesting());

        if ($closingParenClass !== self::class) {
            $parser->parse($unparsed, $closingParenClass);
            return;
        }

        $parser->closeNesting($unparsed, self::class, TOpeningParen::class);
    }
}
