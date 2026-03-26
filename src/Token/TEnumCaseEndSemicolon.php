<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TEnumCaseEndSemicolon extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popNesting(TEnumCase::class);

        $parser->add($source, self::class);
    }
}
