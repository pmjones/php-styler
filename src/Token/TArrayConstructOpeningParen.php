<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TArrayConstructOpeningParen extends ACommaListOpener
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }

    public function commaClass() : string
    {
        return TArrayComma::class;
    }

    public function expandPriority() : ?int
    {
        return ASplittable::BRACKET;
    }
}
