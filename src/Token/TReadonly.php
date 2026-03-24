<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_READONLY
 *
 * Syntax: readonly
 *
 * Reference: https://www.php.net/manual/en/language.oop5.php classes and objects (available as of PHP 8.1.0)
 */
class TReadonly extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->handleModifier();
    }
}
