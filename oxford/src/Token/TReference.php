<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG
 *
 * Syntax: &
 */
class TReference extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $prev = $parser->getPrevParsed();

        $class = match (true) {
            $prev instanceof TVariable,
            $prev instanceof TIntegerLiteral,
            $prev instanceof TFloatLiteral,
            $prev instanceof TStringLiteral,
            $prev?->is(')') ?? false,
            $prev?->is(']') ?? false => TBitwiseAnd::class,

            default => self::class,
        };

        $parser->add($unparsed, $class);
    }
}
