<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TParamsOpeningParen extends AToken implements ACommaListOpener
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }

    public function commaClass() : string
    {
        return TParamsComma::class;
    }

    public function expandPriority() : ?int
    {
        return ASplittable::OTHER_PAREN;
    }
}
