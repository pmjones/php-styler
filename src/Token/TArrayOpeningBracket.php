<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TArrayOpeningBracket extends AToken implements TCommaSeparated
{
    public function commaClass() : string
    {
        return TArrayComma::class;
    }

    public function expandPriority() : ?int
    {
        return TSplittable::BRACKET;
    }

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
