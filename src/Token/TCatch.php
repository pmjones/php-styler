<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_CATCH
 *
 * Syntax: catch
 *
 * Reference: https://www.php.net/manual/en/language.exceptions.php Exceptions
 */
class TCatch extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
        $parser->space();
    }
}
