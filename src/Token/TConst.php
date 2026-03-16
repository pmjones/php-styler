<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_CONST
 *
 * Syntax: const
 *
 * Reference: https://www.php.net/const https://www.php.net/manual/en/language.oop5.constants.php
 */
class TConst extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TUse::class)) {
            $parser->popNesting(TUse::class);
            $parser->addNesting($source, TUseConst::class);
            return;
        }

        $parser->addNesting($source, self::class);
    }
}
