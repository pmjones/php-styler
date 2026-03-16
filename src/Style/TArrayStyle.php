<?php
declare(strict_types=1);

namespace PhpStyler\Style;

class TArrayStyle extends Style
{
    public ?bool $spaceBefore = null;

    public ?bool $spaceAfter = true;

    public mixed $case = 'strtolower';
}
