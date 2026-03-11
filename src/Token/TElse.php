<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_ELSE
 *
 * Syntax: else
 *
 * Reference: https://www.php.net/manual/en/control-structures.else.php else
 */
class TElse extends T
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

        if ($parser->getNextSource()?->is(T_IF)) {
            $parser->add($source, self::class);
            $parser->space();
            return;
        }

        $parser->addNesting($source, self::class);
        $parser->space();

        if (! $parser->getNextSource()?->is(['{', ':'])) {
            $parser->parse($source, TOpeningBraceless::class);
        }
    }
}
