<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AComment;
use PhpStyler\Token\ALineBreaking;
use PhpStyler\Token\AToken;
use PhpStyler\Token\TForSemicolon;
use PhpStyler\Token\TLoopEmptySemicolon;
use PhpStyler\Token\TSpace;

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

            if (! $this->isGap($token)) {
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

    /**
     * Whitespace-like tokens that can legitimately sit between two
     * content tokens without bridging them. Uses an explicit whitelist
     * rather than AToken::isIgnorable() so that synthesized content
     * tokens (emitted by earlier token rules with id=SYNTHETIC) are
     * tracked as content, not treated as gaps.
     */
    private function isGap(AToken $token) : bool
    {
        return $token instanceof TSpace
            || $token instanceof ALineBreaking
            || $token instanceof AComment;
    }
}
