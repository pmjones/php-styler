<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_WHILE
 *
 * Syntax: while
 *
 * Reference: https://www.php.net/manual/en/control-structures.while.php while,
 * https://www.php.net/manual/en/control-structures.do.while.php do..while
 */
class TWhile extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
