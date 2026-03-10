<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_TRY
 *
 * Syntax: try
 *
 * Reference: https://www.php.net/manual/en/language.exceptions.php Exceptions
 */
class TTry extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->addNesting($unparsed, self::class);
        $parser->space();
    }
}
