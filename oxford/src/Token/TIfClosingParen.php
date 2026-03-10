<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TIfClosingParen extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->closeNesting($unparsed, self::class, TIfOpeningParen::class);

        if (! $parser->getNextUnparsed()?->is(['{', ':'])) {
            $parser->parse($unparsed, TOpeningBraceless::class);
        }
    }
}
