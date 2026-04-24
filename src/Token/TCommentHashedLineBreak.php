<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TCommentHashedLineBreak extends AToken implements AComment, ADocblock
{
    use DocblockParsing;
}
