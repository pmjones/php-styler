<?php
declare(strict_types=1);

namespace PhpStyler\Rule\TokenRule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TInlineHtml;

class RemoveLeadingWhitespace extends ATokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        while ($tokens !== []) {
            $token = $tokens[0];

            if (
                $token instanceof TInlineHtml
                && trim($token->text) === ''
            ) {
                array_shift($tokens);
                continue;
            }

            break;
        }

        return $tokens;
    }
}
