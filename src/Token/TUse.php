<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
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
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TClasslikeOpeningBrace::class)) {
            $parser->parse($source, TUseTrait::class);
            return;
        }

        $parser->addNesting($source, self::class);
        $parser->space();
    }
}
