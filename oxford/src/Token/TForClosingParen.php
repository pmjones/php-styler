<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TForClosingParen extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if ($parser->getPrevParsed() instanceof TLoopEmptySemicolon) {
            $parser->parse($unparsed, TLoopEmptyClosingParen::class);
            return;
        }

        $parser->closeNesting($unparsed, self::class, TForOpeningParen::class);

        if (! $parser->getNextUnparsed()?->is(['{', ':', ';'])) {
            $parser->parse($unparsed, TOpeningBraceless::class);
        }
    }
}
