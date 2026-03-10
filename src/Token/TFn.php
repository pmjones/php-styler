<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_FN
 *
 * Syntax: fn
 *
 * Reference: https://www.php.net/manual/en/functions.arrow.php arrow functions (available as of PHP 7.4.0)
 */
class TFn extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
    }
}
