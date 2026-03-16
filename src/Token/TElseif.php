<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ELSEIF
 *
 * Syntax: elseif
 *
 * Reference: https://www.php.net/manual/en/control-structures.elseif.php elseif
 */
class TElseif extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if (
            $parser->atNesting(TIfColon::class)
            || $parser->atNesting(TElseifColon::class)
        ) {
            $parser->popNesting(TIfColon::class, TElseifColon::class);
            $parser->popNesting(TIf::class, TElseif::class);
        }

        $parser->addNesting($source, self::class);
    }
}
