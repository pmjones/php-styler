<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TAnonymousClassArgsClosingParen;
use PhpStyler\Token\TAnonymousClassArgsOpeningParen;
use PhpStyler\Token\TSpace;

class RemoveEmptyAnonymousClassParens extends ATokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if (! ($token instanceof TAnonymousClassArgsOpeningParen)) {
                $result[] = $token;
                continue;
            }

            $nextIdx = $this->findNextContent($tokens, $i + 1, $count);

            if (
                $nextIdx === null
                || ! ($tokens[$nextIdx] instanceof TAnonymousClassArgsClosingParen)
            ) {
                $result[] = $token;
                continue;
            }

            // remove preceding TSpace tokens to avoid double-spacing
            while ($result !== [] && end($result) instanceof TSpace) {
                array_pop($result);
            }

            // skip opening paren through closing paren
            $i = $nextIdx;
        }

        return $result;
    }
}
