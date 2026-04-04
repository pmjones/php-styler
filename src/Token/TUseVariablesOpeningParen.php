<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TUseVariablesOpeningParen extends AToken implements ACommaListOpener
{
    public function commaClass() : string
    {
        return TUseVariablesComma::class;
    }

    public function expandPriority() : ?int
    {
        return ASplittable::OTHER_PAREN;
    }

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
