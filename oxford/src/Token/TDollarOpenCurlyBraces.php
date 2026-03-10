<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_DOLLAR_OPEN_CURLY_BRACES
 *
 * Syntax: ${
 *
 * Reference: https://www.php.net/manual/en/language.types.string.php#language.types.string.parsing.complex complex variable parsed syntax
 */
class TDollarOpenCurlyBraces extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
    }
}
