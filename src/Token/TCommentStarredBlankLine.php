<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TCommentStarredBlankLine extends AToken implements AComment, ADocblock
{
    use DocblockParsing;
}
