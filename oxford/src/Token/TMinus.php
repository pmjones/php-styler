<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TMinus extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $prev = $parser->getPrevParsed();

        if ($prev?->is([
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
            T_INC,
            T_DEC,
        ])) {
            $parser->add($unparsed, TBinaryMinus::class);
            return;
        }

        $parser->add($unparsed, TUnaryMinus::class);
    }
}
