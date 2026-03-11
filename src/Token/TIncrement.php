<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

/**
 * Token: T_INC
 *
 * Syntax: ++
 *
 * Reference: https://www.php.net/manual/en/language.operators.increment.php incrementing/decrementing operators
 */
class TIncrement extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $prev = $parser->getPrevParsed();

        if (
            $prev?->is([
                T_VARIABLE,
                T_LNUMBER,
                T_DNUMBER,
                T_CONSTANT_ENCAPSED_STRING,
                T_STRING,
                T_NAME_QUALIFIED,
                T_NAME_FULLY_QUALIFIED,
                T_NAME_RELATIVE,
                ')',
                ']',
                '}',
            ])
        ) {
            $parser->add($unparsed, TPostIncrement::class);
            return;
        }

        $parser->add($unparsed, TPreIncrement::class);
    }
}
