<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TForSemicolon extends T implements TSplittableComma
{
    public function splitAfter() : ?TSplit
    {
        return new TSplitForSemicolon(T_WHITESPACE, '');
    }

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if (
            $parser->getPrevParsed() instanceof TForOpeningParen
            || $parser->getPrevParsed() instanceof TLoopEmptySemicolon
        ) {
            $parser->add($source, TLoopEmptySemicolon::class);

            return;
        }

        $parser->add($source, self::class);
    }
}
