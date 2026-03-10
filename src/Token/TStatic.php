<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_STATIC
 *
 * Syntax: static
 *
 * Reference: https://www.php.net/manual/en/language.variables.scope.php variable scope
 */
class TStatic extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if ($parser->getNextUnparsed()?->is(T_DOUBLE_COLON)) {

            $parser->add($unparsed, TStaticBinding::class);

            return;
        }

        $prev = $parser->getPrevParsed();

        if (
            $prev instanceof TReturnColon
            || $prev instanceof TNullable
            || $prev instanceof TUnion
            || $prev instanceof TIntersection
        ) {

            $parser->add($unparsed, TStaticType::class);

            return;
        }

        if (
            $parser->getNextUnparsed()?->is(T_VARIABLE)
            && ! $parser->atNesting(TClasslikeOpeningBrace::class)
        ) {

            $parser->addNesting($unparsed, TStaticVar::class);

            return;
        }

        $parser->add($unparsed, self::class);
    }
}
