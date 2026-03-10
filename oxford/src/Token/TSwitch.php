<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_SWITCH
 *
 * Syntax: switch
 *
 * Reference: https://www.php.net/manual/en/control-structures.switch.php switch
 */
class TSwitch extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
        $parser->space();
    }
}
