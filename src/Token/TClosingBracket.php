<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TClosingBracket extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popTernaryNesting();

        if (! $parser->atNesting(TAttribution::class)) {
            /** @var class-string<AToken> $closingBracketClass */
            $closingBracketClass = str_replace(
                'Opening',
                'Closing',
                $parser->getNesting(),
            );

            $parser->parse($source, $closingBracketClass);
            return;
        }

        if ($parser->atNesting(TAttribute::class)) {
            $parser->parse($source, TAttributeClosingBracket::class);
            return;
        }

        $parser->parse($source, TInlineAttributeClosingBracket::class);
    }
}
