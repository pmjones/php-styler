<?php
declare(strict_types=1);

namespace Oxford\Style;

class TDeclareColonStyle extends Style
{
    public ?bool $spaceBefore = false;

    public ?bool $spaceAfter = true;

    public ?bool $lineBreakAfter = true;
}
