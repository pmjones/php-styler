<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TArrayClosingBracket extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->noSpace();
        $parser->closeNesting($source, self::class, TArrayOpeningBracket::class);
        $parser->space();
    }
}
