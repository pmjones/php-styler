<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TSemicolonSkipRepeats extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $prev = $parser->getPrevParsed();

        if (
            $prev !== null
            && $prev->text === ';'
            && ! $prev instanceof TForSemicolon
            && ! $prev instanceof TLoopEmptySemicolon
        ) {
            return;
        }

        TSemicolon::parse($parser, $source);
    }
}
