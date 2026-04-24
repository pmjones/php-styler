<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TCommentSlashedLineBreak extends AToken implements AComment, ADocblock
{
    use DocblockParsing;
}
