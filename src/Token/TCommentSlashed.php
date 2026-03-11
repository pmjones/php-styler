<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TCommentSlashed extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->hasPrevSourceNewline()) {
            $parser->parse($source, TCommentSlashedOwnLine::class);
            return;
        }

        $token = $parser->add($source, TCommentSlashedInline::class);
        $parser->space();
        $transferred = $parser->transferLineBreakAfter($token);

        if (! $transferred && $parser->getNextSource() !== null) {
            $parser->replaceLastParsed($source, TCommentSlashedMidStatement::class);
        }
    }
}
