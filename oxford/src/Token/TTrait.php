<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_TRAIT
 *
 * Syntax: trait
 *
 * Reference: https://www.php.net/manual/en/language.oop5.traits.php Traits
 */
class TTrait extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
        $parser->space();
    }
}
