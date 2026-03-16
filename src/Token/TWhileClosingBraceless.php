<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TWhileClosingBraceless extends T implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popNesting(TWhile::class);
        $parser->indentDecr();
        $parser->add($source, self::class);
    }
}
