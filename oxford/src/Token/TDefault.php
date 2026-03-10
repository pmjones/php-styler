<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

/**
 * Token: T_DEFAULT
 *
 * Syntax: default
 *
 * Reference: https://www.php.net/manual/en/control-structures.switch.php switch
 */
class TDefault extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if ($parser->atNesting(TMatchOpeningBrace::class)) {
            $parser->parse($unparsed, TDefaultMatch::class);
            return;
        }

        if (
            $parser->atNesting(TSwitchOpeningBrace::class)
            || $parser->atNesting(TSwitchColon::class)
            || $parser->atNesting(TCaseColon::class)
        ) {
            $parser->parse($unparsed, TDefaultCase::class);
            return;
        }

        $parser->add($unparsed, self::class);
        $parser->space();
    }
}
