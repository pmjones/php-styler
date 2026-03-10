<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_NAMESPACE
 *
 * Syntax: namespace
 *
 * Reference: https://www.php.net/manual/en/language.namespaces.php namespaces
 */
class TNamespace extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
        $parser->space();
    }
}
