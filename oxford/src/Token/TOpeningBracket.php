<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TOpeningBracket extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if ($parser->getPrevParsed()?->is([T_VARIABLE, T_STRING, ']', ')', '}'])) {
            $parser->parse($unparsed, TArrayElementOpeningBracket::class);
            return;
        }

        $parser->parse($unparsed, TArrayOpeningBracket::class);
    }
}
