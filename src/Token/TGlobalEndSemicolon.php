<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TGlobalEndSemicolon extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popNesting(TGlobal::class);

        $parser->add($source, self::class);
    }
}
