<?php
declare(strict_types=1);

namespace Oxford\Style;

class TConstEndSemicolonStyle extends Style
{
    public ?bool $spaceBefore = false;

    public ?bool $spaceAfter = true;

    public ?bool $blankLineAfter = true;
}
