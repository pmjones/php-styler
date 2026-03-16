<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_DO
 *
 * Syntax: do
 *
 * Reference: https://www.php.net/manual/en/control-structures.do.while.php do..while
 */
class TDo extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
