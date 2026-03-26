<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_FINAL
 *
 * Syntax: final
 *
 * Reference: https://www.php.net/manual/en/language.oop5.final.php Final Keyword
 */
class TFinal extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->handleModifier();
    }
}
