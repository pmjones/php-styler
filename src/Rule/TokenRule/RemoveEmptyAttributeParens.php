<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AnAttribute;
use PhpStyler\Token\AToken;
use PhpStyler\Token\TArgsClosingParen;
use PhpStyler\Token\TArgsOpeningParen;
use PhpStyler\Token\TAttributeClosingBracket;
use PhpStyler\Token\TInlineAttributeClosingBracket;

class RemoveEmptyAttributeParens extends ATokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);
        $inAttribute = false;

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            if ($token instanceof AnAttribute) {
                $inAttribute = true;
            } elseif (
                $token instanceof TAttributeClosingBracket
                || $token instanceof TInlineAttributeClosingBracket
            ) {
                $inAttribute = false;
            }

            if (! $inAttribute || ! ($token instanceof TArgsOpeningParen)) {
                $result[] = $token;
                continue;
            }

            $nextIdx = $this->findNextContent($tokens, $i + 1, $count);

            if (
                $nextIdx === null
                || ! ($tokens[$nextIdx] instanceof TArgsClosingParen)
            ) {
                $result[] = $token;
                continue;
            }

            // skip opening paren through closing paren
            $i = $nextIdx;
        }

        return $result;
    }
}
