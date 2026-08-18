<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TParamsOpeningParen extends ACommaListOpener implements ATypeOpener
{
    public const EXPAND_PRIORITY = ASplittable::OTHER_PAREN;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }

    public function commaClass() : string
    {
        return TParamsComma::class;
    }
}
