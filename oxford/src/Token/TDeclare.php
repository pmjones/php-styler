<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
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
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
        $parser->space();
    }
}
