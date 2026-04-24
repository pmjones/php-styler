<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDocCommentMidStatement extends AToken implements AComment, ADocblock
{
    use DocblockParsing;
}
