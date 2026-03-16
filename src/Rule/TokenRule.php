<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;

interface TokenRule
{
    /**
     * @param T[] $tokens
     * @return T[]
     */
    public function apply(array $tokens) : array;
}
