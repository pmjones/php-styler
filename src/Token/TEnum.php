<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ENUM
 *
 * Syntax: enum
 *
 * Reference: https://www.php.net/manual/en/language.types.enumerations.php Enumerations (available as of PHP 8.1.0)
 */
class TEnum extends AToken
{
    public const OPENING_BRACE = TEnumOpeningBrace::class;

    public const CLOSING_BRACE = TEnumClosingBrace::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
