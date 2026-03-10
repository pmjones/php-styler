<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TCommentSlashed extends T
{
    public static function parse(Parser $parser, PhpToken $unparsed) : void
    {
        if ($parser->hasPrevSourceNewline()) {
            $parser->parse($unparsed, TCommentSlashedOwnLine::class);
            return;
        }

        $token = $parser->add($unparsed, TCommentSlashedInline::class);
        $parser->space();
        $transferred = $parser->transferLineBreakAfter($token);

        if (! $transferred && $parser->getNextUnparsed() !== null) {
            $parser->replaceLastParsed($unparsed, TCommentSlashedMidStatement::class);
        }
    }
}
