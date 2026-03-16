<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
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
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
