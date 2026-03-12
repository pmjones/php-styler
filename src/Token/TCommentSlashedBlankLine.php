<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TCommentSlashedBlankLine extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        $parser->space();
        $parser->add($source, static::class);
        $parser->blankLine();
    }
}
