<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_FOREACH
 *
 * Syntax: foreach
 *
 * Reference: https://www.php.net/manual/en/control-structures.foreach.php for
 */
class TForeach extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
        $parser->space();
    }
}
