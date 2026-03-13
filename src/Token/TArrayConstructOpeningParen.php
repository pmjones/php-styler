<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TArrayConstructOpeningParen extends T implements TCommaSeparated
{
    public function commaClass() : string
    {
        return TArrayComma::class;
    }

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->noSpace();
        $parser->addNesting($source, self::class);
    }
}
