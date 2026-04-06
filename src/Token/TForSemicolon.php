<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TForSemicolon extends AToken implements ASplittableComma
{
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

    public function splitAfter(Parser $parser) : ?TSplit
    {
        return new TSplitForSemicolon(AToken::SYNTHETIC, '');
    }
}
