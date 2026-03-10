<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_YIELD
 *
 * Syntax: yield
 *
 * Reference: https://www.php.net/manual/en/language.generators.syntax.php#control-structures.yield generators
 */
class TYield extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
    }
}
