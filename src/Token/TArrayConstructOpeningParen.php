<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TArrayConstructOpeningParen extends AToken implements ACommaListOpener
{
    public function commaClass() : string
    {
        return TArrayComma::class;
    }

    public function expandPriority() : ?int
    {
        return ASplittable::BRACKET;
    }

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
