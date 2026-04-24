<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TCommentSlashed extends AToken implements ADocblock
{
    use DocblockParsing;

    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->hasPrevLineBreak()) {
            $parser->add($source, TCommentSlashed::class);
            return;
        }

        $parser->add($source, TCommentSlashedMidStatement::class);
    }
}
