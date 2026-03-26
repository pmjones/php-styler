<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TBacktick extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TBacktickOpening::class)) {
            $parser->parse($source, TBacktickClosing::class);
        } else {
            $parser->parse($source, TBacktickOpening::class);
        }
    }
}
