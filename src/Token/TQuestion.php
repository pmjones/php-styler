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
            || $prev instanceof TParamsComma
            || $prev instanceof TPublic
            || $prev instanceof TProtected
            || $prev instanceof TPrivate
            || $prev instanceof TPublicSet
            || $prev instanceof TProtectedSet
            || $prev instanceof TPrivateSet
            || $prev instanceof TReadonly
            || $prev instanceof TVar
            || $prev instanceof TStatic
            || $prev instanceof TConst
        ) {
            $parser->parse($source, TNullable::class);
            return;
        }

        if ($parser->getNextSource()?->is(':')) {
            $parser->parse($source, TElvisQuestion::class);
            return;
        }

        $parser->parse($source, TTernaryQuestion::class);
    }
}
