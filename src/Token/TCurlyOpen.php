<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_CURLY_OPEN
 *
 * Syntax: {$a
 *         ^
 * Reference: https://www.php.net/manual/en/language.types.string.php#language.types.string.parsing.complex complex variable parsed syntax
 */
class TCurlyOpen extends AToken implements AnEncapsedStringOpening
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
