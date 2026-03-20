<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TBooleanAnd;
use PhpStyler\Token\TBooleanOr;
use PhpStyler\Token\TLogicalAnd;
use PhpStyler\Token\TLogicalOr;

class ConvertLogicalOperators implements TokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        foreach ($tokens as $i => $token) {
            if ($token instanceof TLogicalAnd) {
                $tokens[$i] = new TBooleanAnd(
                    T_BOOLEAN_AND,
                    '&&',
                    $token->line,
                    $token->pos,
                );
            } elseif ($token instanceof TLogicalOr) {
                $tokens[$i] = new TBooleanOr(
                    T_BOOLEAN_OR,
                    '||',
                    $token->line,
                    $token->pos,
                );
            }
        }

        return $tokens;
    }
}
