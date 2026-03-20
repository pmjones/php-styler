<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_DEFAULT
 *
 * Syntax: default
 *
 * Reference: https://www.php.net/manual/en/control-structures.switch.php switch
 */
class TDefault extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TMatchOpeningBrace::class)) {
            $parser->parse($source, TDefaultMatch::class);
            return;
        }

        if (
            $parser->atNesting(TSwitchOpeningBrace::class)
            || $parser->atNesting(TSwitchColon::class)
            || $parser->atNesting(TCaseColon::class)
        ) {
            $parser->parse($source, TDefaultCase::class);
            return;
        }

        $parser->add($source, self::class);
    }
}
