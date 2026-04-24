<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TCommentStarredMidStatement extends AToken implements AComment, ADocblock
{
    use DocblockParsing;
}
