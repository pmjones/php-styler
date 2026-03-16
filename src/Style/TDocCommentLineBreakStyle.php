<?php
declare(strict_types=1);

namespace PhpStyler\Style;

class TDocCommentLineBreakStyle extends Style
{
    public ?bool $spaceBefore = true;

    public ?bool $lineBreakAfter = true;
}
