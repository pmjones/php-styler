<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_FUNCTION
 *
 * Syntax: function
 *
 * Reference: https://www.php.net/manual/en/language.functions.php functions
 */
class TFunction extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if ($parser->atNesting(TUse::class)) {
            $parser->popNesting(TUse::class);
            $parser->addNesting($unparsed, TUseFunction::class);
            $parser->space();
            return;
        }

        if ($parser->getNextUnparsed()?->is('(')) {
            $parser->parse($unparsed, TAnonymousFunction::class);
            return;
        }

        $parser->addNesting($unparsed, self::class);
        $parser->space();
    }
}
