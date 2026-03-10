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
class TEllipsis extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $nesting = $parser->getNesting();

        if (
            $nesting === TArgsOpeningParen::class
            && $parser->getPrevParsed() instanceof TArgsOpeningParen
            && $parser->getNextUnparsed()?->is(')')
        ) {
            $parser->add($unparsed, TFirstClassCallableEllipsis::class);
            return;
        }

        $parseClass = match ($nesting) {
            TParamsOpeningParen::class => TVariadicEllipsis::class,
            TArgsOpeningParen::class,
            TArrayOpeningBracket::class,
            TArrayConstructOpeningParen::class => TSpreadEllipsis::class,
            default => self::class,
        };

        $parser->add($unparsed, $parseClass);
    }
}
