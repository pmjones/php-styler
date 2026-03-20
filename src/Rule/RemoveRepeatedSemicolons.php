<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TForSemicolon;
use PhpStyler\Token\TLoopEmptySemicolon;

class RemoveRepeatedSemicolons implements TokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
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

    private function isSemicolon(AToken $token) : bool
    {
        return $token->text === ';'
            && ! $token instanceof TForSemicolon
            && ! $token instanceof TLoopEmptySemicolon;
    }
}
