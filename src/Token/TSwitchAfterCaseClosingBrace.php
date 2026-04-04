<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TSwitchAfterCaseClosingBrace extends AToken implements AClosingStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->removeTrailingBlankLine();
        $parser->indentDecr();
        $parser->indentDecr();
        $parser->closeNesting($source, self::class, TSwitch::class);
    }
}
