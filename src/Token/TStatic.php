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
class TStatic extends AToken implements AModifier
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->getNextSource()?->is(T_DOUBLE_COLON)) {
            $parser->add($source, TStaticBinding::class);

            return;
        }

        $prev = $parser->getPrevParsed();

        if (
            $prev instanceof TReturnColon
            || $prev instanceof TNullable
            || $prev instanceof TUnion
            || $prev instanceof TIntersection
        ) {
            $parser->add($source, TStaticType::class);

            return;
        }

        if (
            $parser->getNextSource()?->is(T_VARIABLE)
            && ! $parser->atNesting(TClasslikeOpeningBrace::class)
        ) {
            $parser->addNesting($source, TStaticVar::class);

            return;
        }

        $parser->handleModifier();
    }
}
