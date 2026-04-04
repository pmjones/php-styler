<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ABSTRACT
 *
 * Syntax: abstract
 *
 * Reference: https://www.php.net/manual/en/language.oop5.abstract.php Class Abstraction
 */
class TAbstract extends AToken implements AModifier
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->handleModifier();
    }
}
