<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TArgsOpeningParen extends AToken implements TCommaSeparated
{
    public function commaClass() : string
    {
        return TArgsComma::class;
    }

    public function expandPriority() : ?int
    {
        return TSplittable::OTHER_PAREN;
    }

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
