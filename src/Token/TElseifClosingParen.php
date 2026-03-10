<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TElseifClosingParen extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->closeNesting($unparsed, self::class, TElseifOpeningParen::class);

        if (! $parser->getNextUnparsed()?->is(['{', ':'])) {
            $parser->parse($unparsed, TOpeningBraceless::class);
        }
    }
}
