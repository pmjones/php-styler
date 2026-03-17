<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
use PhpStyler\Token\TSplittableOperator;

class MergeParenBracket implements TokenRule
{
    /**
     * @param T[] $tokens
     * @return T[]
     */
    public function apply(array $tokens) : array
    {
        $count = count($tokens);

        foreach ($tokens as $start => $token) {
            if (
                $token->text === '('
                && $token->closingToken !== null
                && $token->argCount === 0
            ) {
                $this->check($token, $tokens, (int) $start, $count);
            }
        }

        return $tokens;
    }

    /**
     * @param T[] $tokens
     */
    private function check(T $opener, array $tokens, int $start, int $count) : void
    {
        $containsBracket = false;
        $depth = 0;

        for ($i = $start + 1; $i < $count; $i ++) {
            $token = $tokens[$i];

            if ($token->text === '(' || $token->text === '[') {
                if ($depth === 0 && $token->text === '[') {
                    $containsBracket = true;
                }

                $depth ++;
            } elseif ($token->text === ')' || $token->text === ']') {
                if ($depth === 0) {
                    break;
                }

                $depth --;
            } elseif ($depth === 0 && $token instanceof TSplittableOperator) {
                return;
            }
        }

        if ($containsBracket) {
            $opener->transparentOpener = true;
        }
    }
}
