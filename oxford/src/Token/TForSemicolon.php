<?php
declare(strict_types=1);

namespace Oxford\Token;

use Oxford\Parser;
use PhpToken;

class TForSemicolon extends T implements TSplittableComma
{
    public function splitCategory() : int
    {
        return self::FOR_SEMICOLON;
    }

    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if (
            $parser->getPrevParsed() instanceof TForOpeningParen
            || $parser->getPrevParsed() instanceof TLoopEmptySemicolon
        ) {
            $parser->add($unparsed, TLoopEmptySemicolon::class);

            return;
        }

        $parser->add($unparsed, self::class);
    }
}
