<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_GLOBAL
 *
 * Syntax: global
 *
 * Reference: https://www.php.net/manual/en/language.variables.scope.php variable scope
 */
class TGlobal extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
        $parser->space();
    }
}
