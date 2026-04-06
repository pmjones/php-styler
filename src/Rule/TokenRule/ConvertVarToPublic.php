<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TPublic;
use PhpStyler\Token\TVar;

class ConvertVarToPublic extends ATokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];

        foreach ($tokens as $token) {
            if ($token instanceof TVar && $token->style !== null) {
                $source = new \PhpToken(T_PUBLIC, 'public', $token->line, $token->pos);
                $result[] = AToken::new($source, TPublic::class, $token->style);
            } else {
                $result[] = $token;
            }
        }

        return $result;
    }
}
