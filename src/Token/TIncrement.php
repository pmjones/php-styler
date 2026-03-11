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
    public static function parse(Parser $parser, PhpToken $source) : void
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
            $parser->add($source, TPostIncrement::class);
            return;
        }

        $parser->add($source, TPreIncrement::class);
    }
}
