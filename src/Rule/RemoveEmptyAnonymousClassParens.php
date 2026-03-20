<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TAnonymousClassArgsClosingParen;
use PhpStyler\Token\TAnonymousClassArgsOpeningParen;
use PhpStyler\Token\TSpace;

class RemoveEmptyAnonymousClassParens implements TokenRule
{
    /**
     * @param AToken[] $tokens
     * @return AToken[]
     */
    public function apply(array $tokens) : array
    {
        $result = [];
        $count = count($tokens);
        $skipClosingIds = [];

        for ($i = 0; $i < $count; $i ++) {
            $token = $tokens[$i];

            // skip closing parens marked for removal
            if (
                $token instanceof TAnonymousClassArgsClosingParen
                && isset($skipClosingIds[spl_object_id($token)])
            ) {
                // also skip the TSpace after the closing paren
                if (isset($tokens[$i + 1]) && $tokens[$i + 1] instanceof TSpace) {
                    $i ++;
                }

                continue;
            }

            if (! ($token instanceof TAnonymousClassArgsOpeningParen)) {
                $result[] = $token;
                continue;
            }

            // look ahead past optional TSpace
            $j = $i + 1;

            if ($j < $count && $tokens[$j] instanceof TSpace) {
                $j ++;
            }

            if ($j < $count && $tokens[$j] instanceof TAnonymousClassArgsClosingParen) {
                // empty parens — skip the opening paren and mark closing for removal
                $skipClosingIds[spl_object_id($tokens[$j])] = true;
                continue;
            }

            // not empty, keep the opening paren
            $result[] = $token;
        }

        return $result;
    }
}
