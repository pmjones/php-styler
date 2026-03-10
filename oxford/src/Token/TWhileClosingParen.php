<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TWhileClosingParen extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        $parser->closeNesting($unparsed, self::class, TWhileOpeningParen::class);

        if ($parser->atNesting(TWhile::class) && $parser->getNextUnparsed()?->is(';')) {
            $parser->popNesting(TWhile::class);
        }

        if (! $parser->getNextUnparsed()?->is(['{', ':', ';'])) {
            $parser->parse($unparsed, TOpeningBraceless::class);
        }
    }
}
