<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\T;
use PhpStyler\Token\TArgsClosingParen;
use PhpStyler\Token\TArgsOpeningParen;
use PhpStyler\Token\TAttribute;
use PhpStyler\Token\TAttributeClosingBracket;
use PhpStyler\Token\TInlineAttribute;
use PhpStyler\Token\TInlineAttributeClosingBracket;
use PhpStyler\Token\TSpace;

class RemoveEmptyAttributeParens implements TokenRule
{
    /**
     * @param T[] $tokens
     * @return T[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);
        $skipClosingIds = [];
        $inAttribute = false;

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            // track attribute context
            if (
                $token instanceof TAttribute
                || $token instanceof TInlineAttribute
            ) {
                $inAttribute = true;
            } elseif (
                $token instanceof TAttributeClosingBracket
                || $token instanceof TInlineAttributeClosingBracket
            ) {
                $inAttribute = false;
            }

            // skip closing parens marked for removal
            if (
                $token instanceof TArgsClosingParen
                && isset($skipClosingIds[spl_object_id($token)])
            ) {
                continue;
            }

            if (
                $inAttribute
                && $token instanceof TArgsOpeningParen
            ) {
                // look ahead past optional TSpace
                $j = $i + 1;

                if ($j < $count && $tokens[$j] instanceof TSpace) {
                    $j ++;
                }

                if (
                    $j < $count
                    && $tokens[$j] instanceof TArgsClosingParen
                ) {
                    // empty parens inside attribute — skip opening, mark closing
                    $skipClosingIds[spl_object_id($tokens[$j])] = true;
                    continue;
                }
            }

            $result[] = $token;
        }

        return $result;
    }
}
