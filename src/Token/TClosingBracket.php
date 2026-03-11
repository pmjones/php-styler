<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TClosingBracket extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popTernaryNesting();

        if ($parser->atNesting(TAttribute::class)) {
            $parser->parse($source, TAttributeClosingBracket::class);
            return;
        }

        /** @var class-string<T> $closingBracketClass */
        $closingBracketClass = str_replace('Opening', 'Closing', $parser->getNesting());
        $parser->parse($source, $closingBracketClass);
    }
}
