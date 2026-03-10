<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_ECHO
 *
 * Syntax: echo
 *
 * Reference: https://www.php.net/manual/en/function.echo.php echo
 */
class TEcho extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
    }
}
