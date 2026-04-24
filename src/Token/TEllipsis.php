<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ELLIPSIS
 *
 * Syntax: ...
 *
 * Reference: https://www.php.net/manual/en/functions.arguments.php#functions.variable-arg-list function arguments
 */
class TEllipsis extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $nesting = $parser->getNesting();

        if (
            $nesting === TArgsOpeningParen::class
            && $parser->getPrevParsed() instanceof TArgsOpeningParen
            && $parser->source->peek()?->is(')')
        ) {
            $parser->add($source, TFirstClassCallableEllipsis::class);
            return;
        }

        $parseClass = match ($nesting) {
            TParamsOpeningParen::class => TVariadicEllipsis::class,

            TArgsOpeningParen::class,
            TArrayOpeningBracket::class,
            TArrayConstructOpeningParen::class => TSpreadEllipsis::class,

            // @codeCoverageIgnoreStart
            // defensive: ellipsis only appears in function params, argument
            // lists, or array literals
            default => self::class,
            // @codeCoverageIgnoreEnd
        };

        $parser->add($source, $parseClass);
    }
}
