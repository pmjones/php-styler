<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPropertyHookGetSemicolon extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TPropertyHookGetDoubleArrow::class)) {
            $parser->popNesting(TPropertyHookGetDoubleArrow::class);
        }

        $parser->popNesting(TPropertyHookGet::class);

        $parser->add($source, self::class);
    }
}
