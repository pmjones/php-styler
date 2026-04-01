<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TElseifOpeningParen extends AToken implements TConditionOpener
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }

    public function expandPriority() : ?int
    {
        return TSplittable::CONDITION_PAREN;
    }
}
