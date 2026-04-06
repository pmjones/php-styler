<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPropertyHooksClosingBrace extends AToken implements
    AClosingStructure,
    AMemberClosing
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

    public bool $closesStaticMember = false;

    public function memberType() : string
    {
        return AMemberClosing::PROPERTY;
    }
}
