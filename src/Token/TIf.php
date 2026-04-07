<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_IF
 *
 * Syntax: if
 *
 * Reference: https://www.php.net/manual/en/control-structures.if.php if
 */
class TIf extends AToken
{
    public const OPENING_BRACE = TIfOpeningBrace::class;

    public const CLOSING_BRACE = TIfClosingBrace::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
