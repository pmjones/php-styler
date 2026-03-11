<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_FOR
 *
 * Syntax: for
 *
 * Reference: https://www.php.net/manual/en/control-structures.for.php for
 */
class TFor extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
        $parser->space();
    }
}
