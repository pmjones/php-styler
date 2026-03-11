<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_CASE
 *
 * Syntax: case
 *
 * Reference: https://www.php.net/manual/en/control-structures.switch.php switch
 */
class TCase extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if ($parser->atNesting(TClasslikeOpeningBrace::class)) {
            $parser->parse($unparsed, TEnumCase::class);
            return;
        }

        if ($parser->atNesting(TCaseColon::class)) {
            $parser->popNesting(TCaseColon::class);
            $parser->popNesting(
                TCase::class,
                TDefaultCase::class,
                TCaseAfterCase::class,
                TDefaultAfterCase::class,
            );
            $parser->parse($unparsed, TCaseAfterCase::class);
            return;
        }

        $parser->addNesting($unparsed, self::class);
        $parser->space();
    }
}
