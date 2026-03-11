<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TUseEndSemicolon extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popNesting(TUse::class, TUseFunction::class, TUseConst::class);

        $parser->add($source, self::class);
    }
}
