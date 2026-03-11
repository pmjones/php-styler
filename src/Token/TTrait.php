<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
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
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
        $parser->space();
    }
}
