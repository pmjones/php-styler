<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ENCAPSED_AND_WHITESPACE
 *
 * Syntax: " $a"
 *
 * Reference: https://www.php.net/manual/en/language.types.string.php#language.types.string.parsing constant part of string with variables
 */
class TStringFragment extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->add($unparsed, static::class);
    }
}
