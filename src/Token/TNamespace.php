<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_NAMESPACE
 *
 * Syntax: namespace
 *
 * Reference: https://www.php.net/manual/en/language.namespaces.php namespaces
 */
class TNamespace extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
