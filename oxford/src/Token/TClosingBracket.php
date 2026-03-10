<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TClosingBracket extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->popTernaryNesting();

        if ($parser->atNesting(TAttribute::class)) {
            $parser->parse($unparsed, TAttributeClosingBracket::class);
            return;
        }

        /** @var class-string<T> $closingBracketClass */
        $closingBracketClass = str_replace('Opening', 'Closing', $parser->getNesting());
        $parser->parse($unparsed, $closingBracketClass);
    }
}
