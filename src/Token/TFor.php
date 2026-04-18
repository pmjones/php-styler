<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_FOR
 *
 * Syntax: for
 *
 * Reference: https://www.php.net/manual/en/control-structures.for.php for
 */
class TFor extends AToken
{
    public const OPENING_BRACE = TForOpeningBrace::class;

    public const CLOSING_BRACE = TForClosingBrace::class;

    public const CLOSING_BRACELESS = TForClosingBraceless::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
