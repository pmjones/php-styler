<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPropertyHookSetOpeningParen extends AToken
{
    public function expandPriority() : ?int
    {
        return TSplittable::OTHER_PAREN;
    }

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
