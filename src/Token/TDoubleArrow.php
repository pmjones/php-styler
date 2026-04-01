<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_DOUBLE_ARROW
 *
 * Syntax: =>
 *
 * Reference: https://www.php.net/manual/en/language.types.array.php#language.types.array.syntax array syntax
 */
class TDoubleArrow extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TReturnColon::class)) {
            $parser->popNesting(TReturnColon::class);
        }

        $parseClass = match ($parser->getNesting()) {
            TFn::class => TFnDoubleArrow::class,
            TMatchOpeningBrace::class => TMatchDoubleArrow::class,

            TArrayOpeningBracket::class,
            TArrayConstructOpeningParen::class => TArrayDoubleArrow::class,

            TForeachOpeningParen::class => TForeachDoubleArrow::class,
            TPropertyHookGet::class => TPropertyHookGetDoubleArrow::class,
            TPropertyHookSet::class => TPropertyHookSetDoubleArrow::class,
            TYield::class => TYieldDoubleArrow::class,
            default => null,
        };

        if ($parseClass) {
            $parser->parse($source, $parseClass);
            return;
        }

        $parser->add($source, self::class);
    }
}
