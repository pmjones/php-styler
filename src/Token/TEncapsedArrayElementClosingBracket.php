<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TEncapsedArrayElementClosingBracket extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->noSpace();
        $parser->closeNesting(
            $source,
            self::class,
            TEncapsedArrayElementOpeningBracket::class,
        );
    }
}
