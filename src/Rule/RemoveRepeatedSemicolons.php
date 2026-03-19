<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
use PhpStyler\Token\TForSemicolon;
use PhpStyler\Token\TLoopEmptySemicolon;

class RemoveRepeatedSemicolons implements TokenRule
{
    /**
     * @param T[] $tokens
     * @return T[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $lastContent = null;

        foreach ($tokens as $token) {
            if (
                $this->isSemicolon($token)
                && $lastContent !== null
                && $this->isSemicolon($lastContent)
            ) {
                continue;
            }

            $result[] = $token;

            if (! $token->isIgnorable()) {
                $lastContent = $token;
            }
        }

        return $result;
    }

    private function isSemicolon(T $token) : bool
    {
        return $token->text === ';'
            && ! $token instanceof TForSemicolon
            && ! $token instanceof TLoopEmptySemicolon;
    }
}
