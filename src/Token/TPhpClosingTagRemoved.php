<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TPhpClosingTagRemoved extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->source->peek() === null) {
            return;
        }

        TPhpClosingTag::parse($parser, $source);
    }
}
