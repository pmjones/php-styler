<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPropertyHooksClosingBrace extends AMemberClosing implements AClosingStructure
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->removeTrailingBlankLine();
        $parser->indentDecr();

        $parser->closeNesting(
            $source,
            self::class,
            TPropertyHooksOpeningBrace::class,
        );
    }

    public function memberType() : string
    {
        return AMemberClosing::PROPERTY;
    }
}
