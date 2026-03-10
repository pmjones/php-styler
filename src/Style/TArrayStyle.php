<?php
declare(strict_types=1);

namespace PhpStyler\Style;

class TArrayStyle extends Style
{
    public ?bool $spaceBefore = true;

    public ?bool $spaceAfter = true;

    public mixed $case = 'strtolower';
}
