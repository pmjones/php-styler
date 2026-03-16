<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_DECLARE
 *
 * Syntax: declare
 *
 * Reference: https://www.php.net/manual/en/control-structures.declare.php declare
 */
class TDeclare extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
