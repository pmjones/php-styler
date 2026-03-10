<?php
declare(strict_types=1);

namespace PhpStyler\Style;

class TArrayConstructStyle extends Style
{
    public ?bool $spaceBefore = true;

    public ?bool $spaceAfter = false;

    public mixed $case = 'strtolower';
}
