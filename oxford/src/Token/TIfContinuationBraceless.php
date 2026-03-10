<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TIfContinuationBraceless extends T implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->popNesting(TIf::class);
        $parser->indentDecr();
        $parser->add($unparsed, self::class);
        $parser->space();
    }
}
