<?php
declare(strict_types=1);

namespace PhpStyler\Style;

class TPhpOpeningTagStyle extends Style
{
    public ?bool $spaceBefore = null;

    public ?bool $spaceAfter = true;

    public ?bool $lineBreakAfter = true;
}
