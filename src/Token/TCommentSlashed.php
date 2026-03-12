<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TCommentSlashed extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->hasPrevLineBreak()) {
            $parser->parse($source, TCommentSlashedOwnLine::class);
            return;
        }

        $parser->add($source, TCommentSlashedMidStatement::class);
        $parser->space();
    }
}
