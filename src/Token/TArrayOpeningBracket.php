<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TArrayOpeningBracket extends ACommaListOpener
{
    public const EXPAND_PRIORITY = ASplittable::BRACKET;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }

    public function commaClass() : string
    {
        return TArrayComma::class;
    }
}
