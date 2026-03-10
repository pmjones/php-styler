<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TUseEndSemicolon extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->popNesting(TUse::class, TUseFunction::class, TUseConst::class);

        $parser->add($unparsed, self::class);
    }
}
