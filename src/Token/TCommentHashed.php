<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TCommentHashed extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->hasPrevLineBreak()) {
            $parser->parse($source, TCommentHashedOwnLine::class);
            return;
        }

        $parser->add($source, TCommentHashedMidStatement::class);
        $parser->space();
    }
}
