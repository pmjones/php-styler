<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_NS_SEPARATOR
 *
 * Syntax: \
 *
 * Reference: https://www.php.net/manual/en/language.namespaces.php namespaces
 */
class TNamespaceSeparator extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->add($unparsed, static::class);
    }
}
