<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TUseTraitClosingBrace extends AMemberClosing implements AClosingStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->removeTrailingBlankLine();
        $parser->indentDecr();

        $parser->closeNesting($source, self::class, TUseTrait::class);
    }

    public function memberType() : string
    {
        return AMemberClosing::USE_TRAIT;
    }
}
