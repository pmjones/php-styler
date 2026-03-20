<?php
declare(strict_types=1);

namespace PhpStyler\Rule;

use PhpStyler\Token\AToken;
use PhpStyler\Token\TArgsClosingParen;
use PhpStyler\Token\TArgsOpeningParen;
use PhpStyler\Token\TExit;
use PhpStyler\Token\TSpace;

class AddExitParentheses implements TokenRule
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

            if (! ($token instanceof TExit)) {
                $result[] = $token;
                continue;
            }

            $result[] = $token;

            // look ahead past optional TSpace
            $j = $i + 1;

            if ($j < $count && $tokens[$j] instanceof TSpace) {
                $j ++;
            }

            if ($j < $count && $tokens[$j] instanceof TArgsOpeningParen) {
                // already has parens, skip
                continue;
            }

            // no parens — check if it's a bare exit/die (followed by semicolon or end)
            if ($j >= $count || $tokens[$j]->text === ';') {
                $result[] = new TArgsOpeningParen(AToken::SYNTHETIC, '(');
                $result[] = new TArgsClosingParen(AToken::SYNTHETIC, ')');
            }
        }

        return $result;
    }
}
