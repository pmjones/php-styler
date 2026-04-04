<?php
declare(strict_types=1);

namespace PhpStyler\Token;

use PhpStyler\Docblock;

class TCommentHashedLineBreak extends AToken implements AComment, ADocblock
{
    protected ?Docblock $docblock = null;

    public function getDocblock() : Docblock
    {
        return $this->docblock ??= Docblock::parse($this->text);
    }
}
