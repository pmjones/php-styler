<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_INTERFACE
 *
 * Syntax: interface
 *
 * Reference: https://www.php.net/manual/en/language.oop5.interfaces.php Object Interfaces
 */
class TInterface extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
