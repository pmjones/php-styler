<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_ENUM
 *
 * Syntax: enum
 *
 * Reference: https://www.php.net/manual/en/language.types.enumerations.php Enumerations (available as of PHP 8.1.0)
 */
class TEnum extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
        $parser->space();
    }
}
