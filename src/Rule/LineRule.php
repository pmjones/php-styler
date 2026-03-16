<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Line;

interface LineRule
{
    /**
     * @param Line[] $lines
     * @return Line[]
     */
    public function apply(array $lines) : array;
}
