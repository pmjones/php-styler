<?php
declare(strict_types=1);

namespace PhpStyler\Style;

class TForClosingBracelessStyle extends Style
{
    public ?bool $spaceBefore = false;

    public ?bool $spaceAfter = true;

    public ?bool $lineBreakAfter = true;
}
