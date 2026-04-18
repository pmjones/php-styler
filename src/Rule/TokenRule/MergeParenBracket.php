<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\ACommaListOpener;
use PhpStyler\Token\ASplittableOperator;
use PhpStyler\Token\AToken;

class MergeParenBracket extends ATokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $count = count($tokens);

        foreach ($tokens as $start => $token) {
            if (
                $token->text === '('
                && $token->closingToken !== null
                && (! $token instanceof ACommaListOpener || $token->argCount === 0)
            ) {
                $this->unlinkIfContainsBracket(
                    $token,
                    $tokens,
                    (int) $start,
                    $count,
                );
            }
        }

        return $tokens;
    }

    /**
     * @param AToken[] $tokens
     */
    private function unlinkIfContainsBracket(
        AToken $opener,
        array $tokens,
        int $start,
        int $count,
    ) : void
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
            } elseif ($depth === 0 && $token instanceof ASplittableOperator) {
                return;
            }
        }

        if ($containsBracket && $opener->closingToken !== null) {
            $opener->closingToken->openingToken = null;
            $opener->closingToken = null;
        }
    }
}
