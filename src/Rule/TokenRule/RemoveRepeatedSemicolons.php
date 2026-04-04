<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TForSemicolon;
use PhpStyler\Token\TLoopEmptySemicolon;

class RemoveRepeatedSemicolons extends ATokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $lastContentIdx = null;

        foreach ($tokens as $token) {
            // never remove for-loop or loop-empty semicolons
            if (
                $token instanceof TForSemicolon
                || $token instanceof TLoopEmptySemicolon
            ) {
                $result[] = $token;
                $lastContentIdx = count($result) - 1;
                continue;
            }

            if (
                $this->isSemicolon($token)
                && $lastContentIdx !== null
                && $this->isSemicolon($result[$lastContentIdx])
            ) {
                continue; // skip duplicate
            }

            $result[] = $token;

            if (! $token->isIgnorable()) {
                $lastContentIdx = count($result) - 1;
            }
        }

        return $result;
    }

    private function isSemicolon(AToken $token) : bool
    {
        return $token->text === ';'
            && ! ($token instanceof TForSemicolon)
            && ! ($token instanceof TLoopEmptySemicolon);
    }
}
