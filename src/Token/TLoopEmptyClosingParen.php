<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TLoopEmptyClosingParen extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->closeNesting($unparsed, self::class, TForOpeningParen::class);
        $parser->space();

        if (! $parser->getNextUnparsed()?->is(['{', ':', ';'])) {
            $parser->parse($unparsed, TOpeningBraceless::class);
        }
    }
}
