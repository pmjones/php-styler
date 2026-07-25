<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TQuestion extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $prev = $parser->getPrevParsed();

        if (
            $parser->atNesting(TReturnColon::class)
            || $prev instanceof TParamsOpeningParen
            || $prev instanceof TPropertyHookSetOpeningParen
            || $prev instanceof TParamsComma
            || $prev instanceof AModifier
            || $prev instanceof TConst
        ) {
            $parser->parse($source, TNullable::class);
            return;
        }

        if ($parser->source->peek()?->is(':')) {
            $parser->parse($source, TElvisQuestion::class);
            return;
        }

        $parser->parse($source, TTernaryQuestion::class);
    }
}
