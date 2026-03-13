<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TParamsOpeningParen extends T implements TCommaSeparated
{
    public function commaClass() : string
    {
        return TParamsComma::class;
    }

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->addNesting($source, self::class);
    }
}
