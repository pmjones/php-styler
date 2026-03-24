<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_VAR
 *
 * Syntax: var
 *
 * Reference: https://www.php.net/manual/en/language.oop5.php classes and objects
 */
class TVar extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->handleModifier();
    }
}
