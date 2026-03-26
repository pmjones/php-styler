<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TDoubleQuote extends AToken
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->atNesting(TDoubleQuoteOpening::class)) {
            $parser->parse($source, TDoubleQuoteClosing::class);
        } else {
            $parser->parse($source, TDoubleQuoteOpening::class);
        }
    }
}
