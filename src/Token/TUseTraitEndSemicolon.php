<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TUseTraitEndSemicolon extends AToken implements AMemberClosing
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->popNesting(TUseTrait::class);

        $parser->add($source, self::class);
    }

    public bool $closesStaticMember = false;

    public function memberType() : string
    {
        return AMemberClosing::USE_TRAIT;
    }
}
