<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_WHILE
 *
 * Syntax: while
 *
 * Reference: https://www.php.net/manual/en/control-structures.while.php while,
 * https://www.php.net/manual/en/control-structures.do.while.php do..while
 */
class TWhile extends AToken
{
    public const OPENING_BRACE = TWhileOpeningBrace::class;

    public const CLOSING_BRACE = TWhileClosingBrace::class;

    public const END_SEMICOLON = TDoWhileEndSemicolon::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
