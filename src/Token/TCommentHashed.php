<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Parser;
use PhpToken;

class TCommentHashed extends T
{
    public static function parse(Parser $parser, PhpToken $source) : void
    {
        if ($parser->hasPrevSourceNewline()) {
            $parser->parse($source, TCommentHashedOwnLine::class);
            return;
        }

        $token = $parser->add($source, TCommentHashedInline::class);
        $parser->space();
        $transferred = $parser->transferLineBreakAfter($token);

        if (! $transferred && $parser->getNextSource() !== null) {
            $parser->replaceLastParsed($source, TCommentHashedMidStatement::class);
        }
    }
}
