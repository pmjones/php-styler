<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TPhpClosingTag;

class RemovePhpClosingTag extends ATokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $count = count($tokens);

        if ($count === 0) {
            return $tokens;
        }

        // find last non-whitespace token
        for ($i = $count - 1; $i >= 0; $i --) {
            $token = $tokens[$i];

            if ($token->isIgnorable()) {
                continue;
            }

            if ($token instanceof TPhpClosingTag) {
                // remove from this index to end
                return array_slice($tokens, 0, $i);
            }

            break;
        }

        return $tokens;
    }
}
