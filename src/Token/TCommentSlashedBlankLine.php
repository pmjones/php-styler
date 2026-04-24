<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TCommentSlashedBlankLine extends AToken implements AComment, ADocblock
{
    use DocblockParsing;
}
