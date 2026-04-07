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
class TCatch extends AToken
{
    public const OPENING_BRACE = TCatchOpeningBrace::class;

    public const CLOSING_BRACE = TCatchClosingBrace::class;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
