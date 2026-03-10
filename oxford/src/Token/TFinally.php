<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_FINALLY
 *
 * Syntax: finally
 *
 * Reference: https://www.php.net/manual/en/language.exceptions.php Exceptions
 */
class TFinally extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
        $parser->space();
    }
}
