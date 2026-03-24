<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_PROTECTED
 *
 * Syntax: protected
 *
 * Reference: https://www.php.net/manual/en/language.oop5.php classes and objects
 */
class TProtected extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->handleModifier();
    }
}
