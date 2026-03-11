<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TElvisEndSemicolon extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popNesting(TElvisColon::class);

        $parser->add($source, self::class);
    }
}
