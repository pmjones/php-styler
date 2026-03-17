<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;

class RemoveBom implements TokenRule
{
    private const BOM = "\xEF\xBB\xBF";

    /**
     * @param T[] $tokens
     * @return T[]
     */
    public function apply(array $tokens) : array
    {
        if ($tokens === []) {
            return $tokens;
        }

        if (str_starts_with($tokens[0]->text, self::BOM)) {
            $tokens[0]->text = substr($tokens[0]->text, 3);

            if ($tokens[0]->text === '') {
                array_shift($tokens);
            }
        }

        return $tokens;
    }
}
