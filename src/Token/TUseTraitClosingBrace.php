<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TUseTraitClosingBrace extends AToken implements
    AClosingStructure,
    AMemberClosing
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->removeTrailingBlankLine();
        $parser->indentDecr();

        $parser->closeNesting($source, self::class, TUseTrait::class);
    }

    public bool $closesStaticMember = false;

    public function memberType() : string
    {
        return AMemberClosing::USE_TRAIT;
    }
}
