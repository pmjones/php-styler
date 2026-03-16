<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TLoopEmptyClosingParen extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->closeNesting($source, self::class, TForOpeningParen::class);

        if (! $parser->getNextSource()?->is(['{', ':', ';'])) {
            $parser->parse($source, TOpeningBraceless::class);
        }
    }
}
