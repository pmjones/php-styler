<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
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
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
        $parser->space();
    }
}
