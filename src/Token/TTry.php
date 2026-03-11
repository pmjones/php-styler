<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
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
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
        $parser->space();
    }
}
