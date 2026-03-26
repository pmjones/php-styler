<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_PUBLIC
 *
 * Syntax: public
 *
 * Reference: https://www.php.net/manual/en/language.oop5.php classes and objects
 */
class TPublic extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->handleModifier();
    }
}
