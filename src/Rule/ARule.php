<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;

abstract class ARule
{
    /**
     * Find the index of the next non-ignorable token starting at $from.
     *
     * @param AToken[] $tokens
     */
    protected function findNextContent(array $tokens, int $from, int $count) : ?int
    {
        for ($i = $from; $i < $count; $i ++) {
            if (! $tokens[$i]->isIgnorable()) {
                return $i;
            }
        }

        return null;
    }

    /**
     * Find the index of the previous non-ignorable token in a result array,
     * scanning backward from the end.
     *
     * @param AToken[] $result
     */
    protected function findPrevContent(array $result) : ?int
    {
        for ($i = count($result) - 1; $i >= 0; $i --) {
            if (! $result[$i]->isIgnorable()) {
                return $i;
            }
        }

        return null;
    }
}
