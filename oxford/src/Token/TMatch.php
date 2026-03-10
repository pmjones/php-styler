<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_MATCH
 *
 * Syntax: match
 *
 * Reference: https://www.php.net/manual/en/control-structures.match.php match (available as of PHP 8.0.0)
 */
class TMatch extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
    }
}
