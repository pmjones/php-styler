<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Line;

class RemoveTrailingBlankLines implements LineRule
{
    /**
     * @param Line[] $lines
     * @return Line[]
     */
    public function apply(array $lines) : array
    {
        while ($lines !== [] && end($lines)->isBlank()) {
            array_pop($lines);
        }

        return $lines;
    }
}
