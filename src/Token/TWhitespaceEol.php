<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TWhitespaceEol extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if ($parser->hasPrevEol() || $parser->hasPrevBlankLine()) {
            $parser->blankLine();
            return;
        }

        $parser->add($unparsed, static::class);
    }
}
