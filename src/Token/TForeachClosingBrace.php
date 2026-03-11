<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TForeachClosingBrace extends T implements TClosingStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->removeTrailingBlankLine();
        $parser->indentDecr();

        $parser->closeNesting($source, self::class, TForeach::class);
    }
}
