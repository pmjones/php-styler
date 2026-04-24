<?php
declare(strict_types=1);

namespace PhpStyler\Token;

class TDocCommentLineBreak extends AToken implements AComment, ADocblock
{
    use DocblockParsing;
}
