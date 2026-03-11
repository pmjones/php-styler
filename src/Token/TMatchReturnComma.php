<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TMatchReturnComma extends T implements TSplittableComma
{
    public function splitCategory() : int
    {
        return self::COMMA;
    }

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popNesting(TMatchDoubleArrow::class);

        $parser->add($source, self::class);
    }
}
