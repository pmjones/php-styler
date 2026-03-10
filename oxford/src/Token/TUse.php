<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_USE
 *
 * Syntax: use
 *
 * Reference: https://www.php.net/manual/en/language.namespaces.php namespaces
 */
class TUse extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if ($parser->atNesting(TClasslikeOpeningBrace::class)) {
            $parser->parse($unparsed, TUseTrait::class);
            return;
        }

        $parser->addNesting($unparsed, self::class);
        $parser->space();
    }
}
