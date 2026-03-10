<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_IF
 *
 * Syntax: if
 *
 * Reference: https://www.php.net/manual/en/control-structures.if.php if
 */
class TIf extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
    }
}
